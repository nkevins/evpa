<?php

namespace App\Http\Controllers\Frontend;

use App\Interfaces\Controller;
use App\Models\Bid;
use App\Repositories\Criteria\WhereCriteria;
use App\Repositories\FlightRepository;
use App\Repositories\PirepRepository;
use App\Services\GeoService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

/**
 * Class DashboardController
 */
class DashboardController extends Controller
{
    private $pirepRepo,
            $flightRepo;

    /**
     * DashboardController constructor.
     *
     * @param PirepRepository $pirepRepo
     */
    public function __construct(
        PirepRepository $pirepRepo, 
        FlightRepository $flightRepo
    )
    {
        $this->pirepRepo = $pirepRepo;
        $this->flightRepo = $flightRepo;
    }

    /**
     * Show the application dashboard.
     */
    public function index()
    {
        $last_pirep = null;
        $user = Auth::user();

        try {
            $last_pirep = $this->pirepRepo->find($user->last_pirep_id);
        } catch (\Exception $e) {
        }

        // Get the current airport for the weather
        $current_airport = $user->curr_airport_id ?? $user->home_airport_id;

        return view('dashboard.index', [
            'user'            => $user,
            'current_airport' => $current_airport,
            'last_pirep'      => $last_pirep,
        ]);
    }
    
    /**
     * Show the application dashboard.
     */
    public function indexEpva(Request $request)
    {
        $last_pirep = null;
        $user = Auth::user();
        
        // Get last pirep
        $last_pirep = $this->pirepRepo->getLastPirep($user, 5);

        // Get the current airport for the weather
        $current_airport = $user->curr_airport_id ?? $user->home_airport_id;
        
        // Get random flights
        $departureAirport = null;
        if (setting('pilots.only_flights_from_current')) {
           $departureAirport = Auth::user()->curr_airport_id;
        }
        $flights = $this->flightRepo->getRandomFlight(Auth::user()->airline_id);
        
        // Get current bid
        $bids = Bid::where(['user_id' => $user->id])
            ->with(['flight', 'flight.airline'])->get();
        
        // Get statistic
        $statistics = $this->pirepRepo->getUserPirepStatistic($user);

        return view('dashboard.index', [
            'user'            => $user,
            'current_airport' => $current_airport,
            'last_pirep'      => $last_pirep,
            'flights'         => $flights,
            'bids'            => $bids,
            'stats'           => $statistics,
        ]);
    }
}
