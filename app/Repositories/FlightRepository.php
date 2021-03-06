<?php

namespace App\Repositories;

use App\Interfaces\Repository;
use App\Models\Flight;
use App\Repositories\Criteria\WhereCriteria;
use Illuminate\Http\Request;
use Prettus\Repository\Contracts\CacheableInterface;
use Prettus\Repository\Traits\CacheableRepository;

/**
 * Class FlightRepository
 */
class FlightRepository extends Repository implements CacheableInterface
{
    use CacheableRepository;

    protected $fieldSearchable = [
        'arr_airport_id',
        'dpt_airport_id',
        'flight_number' => 'like',
        'flight_code'   => 'like',
        'flight_leg'    => 'like',
        'route'         => 'like',
        'notes'         => 'like',
    ];

    public function model()
    {
        return Flight::class;
    }

    /**
     * Find a flight based on the given criterea
     *
     * @param      $airline_id
     * @param      $flight_num
     * @param null $route_code
     * @param null $route_leg
     *
     * @return mixed
     */
    public function findFlight($airline_id, $flight_num, $route_code = null, $route_leg = null)
    {
        $where = [
            'airline_id'    => $airline_id,
            'flight_number' => $flight_num,
            'active'        => true,
        ];

        if (filled($route_code)) {
            $where['route_code'] = $route_code;
        }

        if (filled($route_leg)) {
            $where['route_leg'] = $route_leg;
        }

        return $this->findWhere($where);
    }

    /**
     * Get 5 random schedule
     *
     * @param      $airline_id
     * @param null $departure_airport
     *
     * @return Flight
     */
    public function getRandomFlight($airline_id, $departure_airport = null)
    {
        $where = [
            'airline_id' => $airline_id,
            'active'     => true,
        ];

        if (filled($departure_airport)) {
            $where['dpt_airport_id'] = $departure_airport;
        }

        return Flight::inRandomOrder()->where($where)
            ->with('subfleets', 'airline')->take(5)->get();
    }

    /**
     * Create the search criteria and return this with the stuff pushed
     *
     * @param Request $request
     * @param bool    $only_active
     *
     * @throws \Prettus\Repository\Exceptions\RepositoryException
     *
     * @return $this
     */
    public function searchCriteria(Request $request, bool $only_active = true)
    {
        $where = [];

        if ($only_active === true) {
            $where['active'] = $only_active;
            $where['visible'] = $only_active;
        }

        if ($request->filled('flight_id')) {
            $where['id'] = $request->flight_id;
        }

        if ($request->filled('airline_id')) {
            $where['airline_id'] = $request->airline_id;
        }

        if ($request->filled('flight_number')) {
            $where['flight_number'] = $request->flight_number;
        }

        if ($request->filled('route_code')) {
            $where['route_code'] = $request->route_code;
        }

        if ($request->filled('dep_icao')) {
            $where['dpt_airport_id'] = $request->dep_icao;
        }

        if ($request->filled('arr_icao')) {
            $where['arr_airport_id'] = $request->arr_icao;
        }

        $this->pushCriteria(new WhereCriteria($request, $where));

        return $this;
    }

    /**
     * Get all airports which is used in schedule
     *
     * @param   $airline_id
     *
     * @return Airport
     */
    public function getFlightAirport($airline_id)
    {
        $all_flights = Flight::with('dpt_airport', 'arr_airport')
                            ->where('airline_id', $airline_id)
                            ->where('active', 1)
                            ->where('visible', 1)->get();

        $airports = [];
        foreach ($all_flights as $f) {
            if (!isset($airports[$f->dpt_airport_id])) {
                $airports[$f->dpt_airport_id] = $f->dpt_airport;
            }

            if (!isset($airports[$f->arr_airport_id])) {
                $airports[$f->arr_airport_id] = $f->arr_airport;
            }
        }

        return $airports;
    }

    /**
     * Get all destination airports from a departure point
     *
     * @param   $airline_id
     * @param   $departure
     *
     * @return Airport
     */
    public function getDestinationAirport($airline_id, $departure)
    {
        $all_airports = Flight::with('arr_airport')
                            ->where('airline_id', $airline_id)
                            ->where('dpt_airport_id', $departure)
                            ->where('active', 1)
                            ->where('visible', 1)->get();

        $airports = [];
        foreach ($all_airports as $a) {
            if (!isset($airports[$a->arr_airport_id])) {
                $airports[$a->arr_airport_id] = $a->arr_airport;
            }
        }

        return $airports;
    }
}
