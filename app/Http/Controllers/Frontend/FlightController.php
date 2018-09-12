<?php

namespace App\Http\Controllers\Frontend;

use App\Interfaces\Controller;
use App\Models\Bid;
use App\Models\Flight;
use App\Repositories\AirlineRepository;
use App\Repositories\AirportRepository;
use App\Repositories\Criteria\WhereCriteria;
use App\Repositories\FlightRepository;
use App\Repositories\SubfleetRepository;
use App\Services\GeoService;
use Flash;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Log;
use Prettus\Repository\Exceptions\RepositoryException;

/**
 * Class FlightController
 */
class FlightController extends Controller
{
    private $airlineRepo;
    private $airportRepo;
    private $flightRepo;
    private $subfleetRepo;
    private $geoSvc;

    /**
     * FlightController constructor.
     *
     * @param AirlineRepository $airlineRepo
     * @param AirportRepository $airportRepo
     * @param FlightRepository  $flightRepo
     * @param GeoService        $geoSvc
     */
    public function __construct(
        AirlineRepository $airlineRepo,
        AirportRepository $airportRepo,
        FlightRepository $flightRepo,
        SubfleetRepository $subfleetRepo,
        GeoService $geoSvc
    ) {
        $this->airlineRepo = $airlineRepo;
        $this->airportRepo = $airportRepo;
        $this->flightRepo = $flightRepo;
        $this->subfleetRepo = $subfleetRepo;
        $this->geoSvc = $geoSvc;
    }

    /**
     * @param Request $request
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index(Request $request)
    {
        $where = [
            'active'  => true,
            'visible' => true,
        ];

        if (setting('pilots.restrict_to_company')) {
            $where['airline_id'] = Auth::user()->airline_id;
        }

        // default restrictions on the flights shown. Handle search differently
        if (setting('pilots.only_flights_from_current')) {
            $where['dpt_airport_id'] = Auth::user()->curr_airport_id;
        }

        try {
            $this->flightRepo->pushCriteria(new WhereCriteria($request, $where));
        } catch (RepositoryException $e) {
            Log::emergency($e);
        }

        $flights = $this->flightRepo
            ->with(['dpt_airport', 'arr_airport', 'airline'])
            ->orderBy('flight_number', 'asc')
            ->orderBy('route_leg', 'asc')
            ->paginate();

        $saved_flights = Bid::where('user_id', Auth::id())
            ->pluck('flight_id')->toArray();

        return view('flights.index', [
            'airlines' => $this->airlineRepo->selectBoxList(true),
            'airports' => $this->airportRepo->selectBoxList(true),
            'flights'  => $flights,
            'saved'    => $saved_flights,
        ]);
    }

    /**
     * @param Request $request
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function indexEvpa(Request $request)
    {
        $where = [
            'active'  => true,
            'visible' => true,
        ];

        $airlineId = Auth::user()->airline_id;
        if (setting('pilots.restrict_to_company')) {
            $where['airline_id'] = $airlineId;
        }

        // default restrictions on the flights shown. Handle search differently
        if (setting('pilots.only_flights_from_current')) {
            $where['dpt_airport_id'] = Auth::user()->curr_airport_id;
        }

        try {
            $this->flightRepo->pushCriteria(new WhereCriteria($request, $where));
        } catch (RepositoryException $e) {
            Log::emergency($e);
        }

        $flights = $this->flightRepo
            ->with(['dpt_airport', 'arr_airport', 'airline', 'subfleets'])
            ->orderBy('flight_number', 'asc')
            ->orderBy('route_leg', 'asc')
            ->paginate();

        $saved_flights = Bid::where('user_id', Auth::id())
            ->pluck('flight_id')->toArray();

        // Get airport list for search
        $all_flights = Flight::with('dpt_airport', 'arr_airport')
                            ->where($where)->get();

        $dep_apts[''] = '';
        $arr_apts[''] = '';

        foreach ($all_flights as $f) {
            $dep_apts[$f->dpt_airport->icao] = $f->dpt_airport->icao.' - '.$f->dpt_airport->name;
            ksort($dep_apts);

            $arr_apts[$f->arr_airport->icao] = $f->arr_airport->icao.' - '.$f->arr_airport->name;
            ksort($arr_apts);
        }

        // Get aircraft list for search
        $subfleets_list[''] = '';
        $subfleets = $this->subfleetRepo->findWhere(['airline_id' => $airlineId]);
        foreach ($subfleets as $sf) {
            $subfleets_list[$sf->type] = $sf->name;
        }

        return view('flights.index', [
            'flights'   => $flights,
            'saved'     => $saved_flights,
            'dep_apts'  => $dep_apts,
            'arr_apts'  => $arr_apts,
            'subfleets' => $subfleets_list,
        ]);
    }

    /**
     * Find the user's bids and display them
     *
     * @param Request $request
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function bids(Request $request)
    {
        $user = Auth::user();

        $flights = $user->flights()->paginate();
        $saved_flights = $flights->pluck('id')->toArray();

        return view('flights.index', [
            'title'    => trans_choice('flights.mybid', 2),
            'airlines' => $this->airlineRepo->selectBoxList(true),
            'airports' => $this->airportRepo->selectBoxList(true),
            'flights'  => $flights,
            'saved'    => $saved_flights,
        ]);
    }

    /**
     * Find the user's bids and display them
     *
     * @param Request $request
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function bidsEvpa(Request $request)
    {
        $user = Auth::user();

        $flights = $user->flights()->paginate();
        $saved_flights = $flights->pluck('id')->toArray();

        return view('flights.bids', [
            'flights' => $flights,
            'saved'   => $saved_flights,
        ]);
    }

    /**
     * Make a search request using the Repository search
     *
     * @param Request $request
     *
     * @throws \Prettus\Repository\Exceptions\RepositoryException
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function search(Request $request)
    {
        $where = [
            'active'  => true,
            'visible' => true,
        ];

        if (setting('pilots.restrict_to_company')) {
            $where['airline_id'] = Auth::user()->airline_id;
        }

        // default restrictions on the flights shown. Handle search differently
        if (setting('pilots.only_flights_from_current')) {
            $where['dpt_airport_id'] = Auth::user()->curr_airport_id;
        }

        try {
            $this->flightRepo->pushCriteria(new WhereCriteria($request, $where));
        } catch (RepositoryException $e) {
            Log::emergency($e);
        }

        $flights = $this->flightRepo->searchCriteria($request)
            ->with(['dpt_airport', 'arr_airport', 'airline'])
            ->orderBy('flight_number', 'asc')
            ->orderBy('route_leg', 'asc')
            ->paginate();

        $saved_flights = Bid::where('user_id', Auth::id())
            ->pluck('flight_id')->toArray();

        return view('flights.index', [
            'airlines' => $this->airlineRepo->selectBoxList(true),
            'airports' => $this->airportRepo->selectBoxList(true),
            'flights'  => $flights,
            'saved'    => $saved_flights,
        ]);
    }

    /**
     * Make a search request using the Repository search
     *
     * @param Request $request
     *
     * @throws \Prettus\Repository\Exceptions\RepositoryException
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function searchEvpa(Request $request)
    {
        $where = [
            'active'  => true,
            'visible' => true,
        ];

        $airlineId = Auth::user()->airline_id;
        if (setting('pilots.restrict_to_company')) {
            $where['airline_id'] = $airlineId;
        }

        // default restrictions on the flights shown. Handle search differently
        if (setting('pilots.only_flights_from_current')) {
            $where['dpt_airport_id'] = Auth::user()->curr_airport_id;
        }

        try {
            $this->flightRepo->pushCriteria(new WhereCriteria($request, $where));
        } catch (RepositoryException $e) {
            Log::emergency($e);
        }

        $flights = $this->flightRepo->searchCriteria($request)
            ->with(['dpt_airport', 'arr_airport', 'airline'])
            ->orderBy('flight_number', 'asc')
            ->orderBy('route_leg', 'asc')
            ->paginate();

        $saved_flights = Bid::where('user_id', Auth::id())
            ->pluck('flight_id')->toArray();

        // Get airport list for search
        $all_flights = Flight::with('dpt_airport', 'arr_airport')
                            ->where($where)->get();

        $dep_apts[''] = '';
        $arr_apts[''] = '';

        foreach ($all_flights as $f) {
            $dep_apts[$f->dpt_airport->icao] = $f->dpt_airport->icao.' - '.$f->dpt_airport->name;
            ksort($dep_apts);

            $arr_apts[$f->arr_airport->icao] = $f->arr_airport->icao.' - '.$f->arr_airport->name;
            ksort($arr_apts);
        }

        // Get aircraft list for search
        $subfleets_list[''] = '';
        $subfleets = $this->subfleetRepo->findWhere(['airline_id' => $airlineId]);
        foreach ($subfleets as $sf) {
            $subfleets_list[$sf->type] = $sf->name;
        }

        return view('flights.index', [
            'flights'   => $flights,
            'saved'     => $saved_flights,
            'dep_apts'  => $dep_apts,
            'arr_apts'  => $arr_apts,
            'subfleets' => $subfleets_list,
        ]);
    }

    /**
     * Show the flight information page
     *
     * @param $id
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector|\Illuminate\View\View
     */
    public function show($id)
    {
        $flight = $this->flightRepo->find($id);
        if (empty($flight)) {
            Flash::error('Flight not found!');
            return redirect(route('frontend.dashboard.index'));
        }

        $map_features = $this->geoSvc->flightGeoJson($flight);

        return view('flights.show', [
            'flight'       => $flight,
            'map_features' => $map_features,
        ]);
    }

    /**
     * Show the flight routes page
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector|\Illuminate\View\View
     */
    public function showFlightRoutes()
    {
        return view('flights.routes');
    }
}
