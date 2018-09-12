<?php

namespace App\Http\Controllers\Api;

use App\Http\Resources\Flight as FlightResource;
use App\Http\Resources\Navdata as NavdataResource;
use App\Interfaces\Controller;
use App\Repositories\Criteria\WhereCriteria;
use App\Repositories\FlightRepository;
use App\Services\FlightService;
use App\Services\GeoService;
use Auth;
use Illuminate\Http\Request;
use Prettus\Repository\Criteria\RequestCriteria;
use Prettus\Repository\Exceptions\RepositoryException;

/**
 * Class FlightController
 */
class FlightController extends Controller
{
    private $flightRepo;
    private $flightSvc;
    private $geoSvc;

    /**
     * FlightController constructor.
     *
     * @param FlightRepository $flightRepo
     * @param FlightService    $flightSvc
     */
    public function __construct(
        FlightRepository $flightRepo,
        FlightService $flightSvc,
        GeoService $geoSvc
    ) {
        $this->flightRepo = $flightRepo;
        $this->flightSvc = $flightSvc;
        $this->geoSvc = $geoSvc;
    }

    /**
     * Return all the flights, paginated
     *
     * @param Request $request
     *
     * @return \Illuminate\Http\Resources\Json\AnonymousResourceCollection
     */
    public function index(Request $request)
    {
        $user = Auth::user();

        $where = [
            'active'  => true,
            'visible' => true,
        ];

        if (setting('pilots.restrict_to_company')) {
            $where['airline_id'] = Auth::user()->airline_id;
        }
        if (setting('pilots.only_flights_from_current', false)) {
            $where['dpt_airport_id'] = $user->curr_airport_id;
        }

        $flights = $this->flightRepo
            ->whereOrder($where, 'flight_number', 'asc')
            ->paginate();

        foreach ($flights as $flight) {
            $this->flightSvc->filterSubfleets($user, $flight);
        }

        return FlightResource::collection($flights);
    }

    /**
     * @param $id
     *
     * @return FlightResource
     */
    public function get($id)
    {
        $flight = $this->flightRepo->find($id);
        $this->flightSvc->filterSubfleets(Auth::user(), $flight);

        return new FlightResource($flight);
    }

    /**
     * @param Request $request
     *
     * @return mixed
     */
    public function search(Request $request)
    {
        $user = Auth::user();

        try {
            $where = [
                'active'  => true,
                'visible' => true,
            ];

            if (setting('pilots.restrict_to_company')) {
                $where['airline_id'] = Auth::user()->airline_id;
            }
            if (setting('pilots.only_flights_from_current')) {
                $where['dpt_airport_id'] = Auth::user()->curr_airport_id;
            }

            $this->flightRepo->searchCriteria($request);
            $this->flightRepo->pushCriteria(new RequestCriteria($request));
            $this->flightRepo->pushCriteria(new WhereCriteria($request, $where));
            $flights = $this->flightRepo->paginate();
        } catch (RepositoryException $e) {
            return response($e, 503);
        }

        foreach ($flights as $flight) {
            $this->flightSvc->filterSubfleets($user, $flight);
        }

        return FlightResource::collection($flights);
    }

    /**
     * Get a flight's route
     *
     * @param         $id
     * @param Request $request
     *
     * @return \Illuminate\Http\Resources\Json\AnonymousResourceCollection
     */
    public function route($id, Request $request)
    {
        $flight = $this->flightRepo->find($id);
        $route = $this->flightSvc->getRoute($flight);

        return NavdataResource::collection($route);
    }

    /**
     * Get all airport points flown by an airline
     *
     * @param         $airline_id
     * @param Request $request
     *
     * @return \Illuminate\Http\Resources\Json\AnonymousResourceCollection
     */
    public function route_airports_geojson($airline_id, Request $request)
    {
        $airports = $this->flightRepo->getFlightAirport($airline_id);
        $map_features = $this->geoSvc->scheduleMapAirportGeoJson($airports);

        return $map_features;
    }

    /**
     * Get all route lines from a departure airport
     *
     * @param         $airline_id
     * @param         $departure
     * @param Request $request
     *
     * @return \Illuminate\Http\Resources\Json\AnonymousResourceCollection
     */
    public function route_destination_geojson($airline_id, $departure, Request $request)
    {
        $destination_airports = $this->flightRepo->getDestinationAirport($airline_id, $departure);
        $map_features = $this->geoSvc->scheduleMapRouteGeoJson($departure, $destination_airports);

        return $map_features;
    }
}
