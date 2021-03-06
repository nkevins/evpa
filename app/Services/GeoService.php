<?php

namespace App\Services;

use App\Interfaces\Service;
use App\Models\Acars;
use App\Models\Enums\AcarsType;
use App\Models\Flight;
use App\Models\GeoJson;
use App\Models\Pirep;
use App\Repositories\AcarsRepository;
use App\Repositories\AirportRepository;
use App\Repositories\NavdataRepository;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Collection;
use League\Geotools\Coordinate\Coordinate;
use League\Geotools\Geotools;
use Log;

/**
 * Class GeoService
 */
class GeoService extends Service
{
    private $acarsRepo;
    private $navRepo;
    private $airportRepo;

    /**
     * GeoService constructor.
     *
     * @param AcarsRepository   $acarsRepo
     * @param NavdataRepository $navRepo
     */
    public function __construct(
        AcarsRepository $acarsRepo,
        NavdataRepository $navRepo,
        AirportRepository $airportRepo
    ) {
        $this->acarsRepo = $acarsRepo;
        $this->navRepo = $navRepo;
        $this->airportRepo = $airportRepo;
    }

    /**
     * Determine the closest set of coordinates from the starting position
     *
     * @param array $coordStart
     * @param array $all_coords
     *
     * @throws \League\Geotools\Exception\InvalidArgumentException
     *
     * @return mixed
     */
    public function getClosestCoords($coordStart, $all_coords)
    {
        $distance = [];
        $geotools = new Geotools();
        $start = new Coordinate($coordStart);

        foreach ($all_coords as $coords) {
            $coord = new Coordinate($coords);
            $dist = $geotools->distance()->setFrom($start)->setTo($coord);
            $distance[] = $dist->greatCircle();
        }

        $distance = collect($distance);
        $min = $distance->min();

        return $all_coords[$distance->search($min, true)];
    }

    /**
     * Pass in a route string, with the departure/arrival airports, and the
     * starting coordinates. Return the route points that have been found
     * from the `navdata` table
     *
     * @param $dep_icao     string  ICAO to ignore
     * @param $arr_icao     string  ICAO to ignore
     * @param $start_coords array   Starting point, [x, y]
     * @param $route        string  Textual route
     *
     * @return array
     */
    public function getCoordsFromRoute($dep_icao, $arr_icao, $start_coords, $route): array
    {
        $coords = [];
        $filter_points = [$dep_icao, $arr_icao, 'SID', 'STAR'];

        $split_route = collect(explode(' ', $route))->transform(function ($point) {
            if (empty($point)) {
                return false;
            }

            return strtoupper(trim($point));
        })->filter(function ($point) use ($filter_points) {
            return !(empty($point) || \in_array($point, $filter_points, true));
        });

        /**
         * @var $split_route Collection
         * @var $route_point Acars
         */
        foreach ($split_route as $route_point) {
            Log::debug('Looking for '.$route_point);

            try {
                $points = $this->navRepo->findWhere(['id' => $route_point]);
            } catch (ModelNotFoundException $e) {
                continue;
            } catch (\Exception $e) {
                Log::error($e);
                continue;
            }

            $size = \count($points);

            if ($size === 0) {
                continue;
            } elseif ($size === 1) {
                $point = $points[0];
                Log::debug('name: '.$point->id.' - '.$point->lat.'x'.$point->lon);
                $coords[] = $point;
                continue;
            }

            // Find the point with the shortest distance
            Log::info('found '.$size.' for '.$route_point);

            // Get the start point and then reverse the lat/lon reference
            // If the first point happens to have multiple possibilities, use
            // the starting point that was passed in
            if (\count($coords) > 0) {
                $start_point = $coords[\count($coords) - 1];
                $start_point = [$start_point->lat, $start_point->lon];
            } else {
                $start_point = $start_coords;
            }

            // Put all of the lat/lon sets into an array to pick of what's clsest
            // to the starting point
            $potential_coords = [];
            foreach ($points as $point) {
                $potential_coords[] = [$point->lat, $point->lon];
            }

            // returns an array with the closest lat/lon to start point
            $closest_coords = $this->getClosestCoords($start_point, $potential_coords);
            foreach ($points as $point) {
                if ($point->lat === $closest_coords[0] && $point->lon === $closest_coords[1]) {
                    break;
                }
            }

            $coords[] = $point;
        }

        return $coords;
    }

    /**
     * Determine the center point between two sets of coordinates
     *
     * @param $latA
     * @param $lonA
     * @param $latB
     * @param $lonB
     *
     * @throws \League\Geotools\Exception\InvalidArgumentException
     *
     * @return array
     */
    public function getCenter($latA, $lonA, $latB, $lonB)
    {
        $geotools = new Geotools();
        $coordA = new Coordinate([$latA, $lonA]);
        $coordB = new Coordinate([$latB, $lonB]);

        $vertex = $geotools->vertex()->setFrom($coordA)->setTo($coordB);
        $middlePoint = $vertex->middle();

        $center = [
            $middlePoint->getLatitude(),
            $middlePoint->getLongitude(),
        ];

        return $center;
    }

    /**
     * Read an array/relationship of ACARS model points
     *
     * @param Pirep $pirep
     *
     * @return array
     */
    public function getFeatureFromAcars(Pirep $pirep)
    {
        // Get the two airports
        $airports = new GeoJson();
        $airports->addPoint($pirep->dpt_airport->lat, $pirep->dpt_airport->lon, [
            'name' => $pirep->dpt_airport->name,
            'icao' => $pirep->dpt_airport->icao,
            'type' => 'D',
        ]);

        $airports->addPoint($pirep->arr_airport->lat, $pirep->arr_airport->lon, [
            'name' => $pirep->arr_airport->name,
            'icao' => $pirep->arr_airport->icao,
            'type' => 'A',
        ]);

        $route = new GeoJson();

        $actual_route = $this->acarsRepo->forPirep($pirep->id, AcarsType::FLIGHT_PATH);
        foreach ($actual_route as $point) {
            $route->addPoint($point->lat, $point->lon, [
                'pirep_id' => $pirep->id,
                'alt'      => $point->altitude,
                //'popup'    => 'GS: '.$point->gs.'<br />Alt: '.$point->altitude,
            ]);
        }

        /*
         * @var $point \App\Models\Acars
         */
        /*foreach ($pirep->acars as $point) {
            $route->addPoint($point->lat, $point->lon, [
                'pirep_id' => $pirep->id,
                'alt'      => $point->altitude,
            ]);
        }*/

        return [
            'position' => [
                'lat' => $pirep->position->lat,
                'lon' => $pirep->position->lon,
            ],
            'line'     => $route->getLine(),
            'points'   => $route->getPoints(),
            'airports' => [
                'a' => [
                    'icao' => $pirep->arr_airport->icao,
                    'lat'  => $pirep->arr_airport->lat,
                    'lon'  => $pirep->arr_airport->lon,
                ],
                'd' => [
                    'icao' => $pirep->dpt_airport->icao,
                    'lat'  => $pirep->dpt_airport->lat,
                    'lon'  => $pirep->dpt_airport->lon,
                ],
            ],
        ];
    }

    /**
     * Return a single feature point for the
     *
     * @param mixed $pireps
     *
     * @return mixed
     * @return \GeoJson\Feature\FeatureCollection
     */
    public function getFeatureForLiveFlights($pireps)
    {
        $flight = new GeoJson();

        /**
         * @var Pirep $pirep
         */
        foreach ($pireps as $pirep) {
            /**
             * @var $point \App\Models\Acars
             */
            $point = $pirep->position;
            if (!$point) {
                continue;
            }

            $flight->addPoint($point->lat, $point->lon, [
                'pirep_id' => $pirep->id,
                'alt'      => $point->altitude,
                'heading'  => $point->heading ?: 0,
            ]);
        }

        return $flight->getPoints();
    }

    /**
     * Return a FeatureCollection GeoJSON object
     *
     * @param Flight $flight
     *
     * @return array
     */
    public function flightGeoJson(Flight $flight): array
    {
        $route = new GeoJson();

        //# Departure Airport
        $route->addPoint($flight->dpt_airport->lat, $flight->dpt_airport->lon, [
            'name'  => $flight->dpt_airport->icao,
            'popup' => $flight->dpt_airport->full_name,
            'icon'  => 'airport',
        ]);

        if ($flight->route) {
            $all_route_points = $this->getCoordsFromRoute(
                $flight->dpt_airport->icao,
                $flight->arr_airport->icao,
                [$flight->dpt_airport->lat, $flight->dpt_airport->lon],
                $flight->route);

            // lat, lon needs to be reversed for GeoJSON
            foreach ($all_route_points as $point) {
                $route->addPoint($point->lat, $point->lon, [
                    'name'  => $point->name,
                    'popup' => $point->name.' ('.$point->name.')',
                    'icon'  => '',
                ]);
            }
        }

        $route->addPoint($flight->arr_airport->lat, $flight->arr_airport->lon, [
            'name'  => $flight->arr_airport->icao,
            'popup' => $flight->arr_airport->full_name,
            'icon'  => 'airport',
        ]);

        return [
            'route_points'       => $route->getPoints(),
            'planned_route_line' => $route->getLine(),
        ];
    }

    /**
     * Return a GeoJSON FeatureCollection for a PIREP
     *
     * @param Pirep $pirep
     *
     * @return array
     */
    public function pirepGeoJson(Pirep $pirep)
    {
        $planned = new GeoJson();
        $actual = new GeoJson();

        /*
         * PLANNED ROUTE
         */
        $planned->addPoint($pirep->dpt_airport->lat, $pirep->dpt_airport->lon, [
            'name'  => $pirep->dpt_airport->icao,
            'popup' => $pirep->dpt_airport->full_name,
        ]);

        $planned_route = $this->acarsRepo->forPirep($pirep->id, AcarsType::ROUTE);
        foreach ($planned_route as $point) {
            $planned->addPoint($point->lat, $point->lon, [
                'name'  => $point->name,
                'popup' => $point->name.' ('.$point->name.')',
            ]);
        }

        $planned->addPoint($pirep->arr_airport->lat, $pirep->arr_airport->lon, [
            'name'  => $pirep->arr_airport->icao,
            'popup' => $pirep->arr_airport->full_name,
            'icon'  => 'airport',
        ]);

        /**
         * ACTUAL ROUTE
         */
        $actual_route = $this->acarsRepo->forPirep($pirep->id, AcarsType::FLIGHT_PATH);
        foreach ($actual_route as $point) {
            $actual->addPoint($point->lat, $point->lon, [
                'pirep_id' => $pirep->id,
                'name'     => $point->altitude,
                'popup'    => 'GS: '.$point->gs.'<br />Alt: '.$point->altitude,
            ]);
        }

        return [
            'planned_rte_points' => $planned->getPoints(),
            'planned_rte_line'   => $planned->getLine(),

            'actual_route_points' => $actual->getPoints(),
            'actual_route_line'   => $actual->getLine(),
        ];
    }

    /**
     * Return a GeoJSON FeatureCollection for a voyage map
     *
     * @param $voyages
     *
     * @return array
     */
    public function voyageGeoJson($voyages)
    {
        $routes = [];
        $route = new GeoJson();

        foreach ($voyages as $v) {
            //# Departure Airport
            $route->addPoint($v->dep_lat, $v->dep_lon, [
                'name'  => $v->dep_icao,
                'popup' => $v->dep_name,
                'icon'  => 'airport',
            ]);

            //# Destination Airport
            $route->addPoint($v->des_lat, $v->des_lon, [
                'name'  => $v->des_icao,
                'popup' => $v->des_name,
                'icon'  => 'airport',
            ]);

            array_push($routes, ['route_points' => $route->getPoints(),
                'planned_route_line'            => $route->getLine(), ]);
        }

        return $routes;
    }

    /**
     * Return a GeoJSON FeatureCollection for airport points
     *
     * @param $airports
     *
     * @return array
     */
    public function scheduleMapAirportGeoJson($airports)
    {
        $apts = new GeoJson();

        foreach ($airports as $a) {
            $apts->addPoint($a->lat, $a->lon, [
                'name'  => $a->icao,
                'popup' => $a->icao.' - '.$a->name,
                'icon'  => 'airport',
            ]);
        }

        return $apts->getPoints();
    }

    /**
     * Return a GeoJSON FeatureCollection for route lines
     * from a departure point to all destinations
     *
     * @param $departure
     * @param $destinations
     *
     * @return array
     */
    public function scheduleMapRouteGeoJson($departure, $destinations)
    {
        $dep = $this->airportRepo->find($departure);

        $routes = new GeoJson();

        foreach ($destinations as $arr) {
            $routes->addPoint($dep->lat, $dep->lon, []);
            $routes->addPoint($arr->lat, $arr->lon, []);
        }

        return $routes->getLine();
    }
}
