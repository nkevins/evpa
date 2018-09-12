<?php

namespace App\Http\Controllers\Frontend;

use App\Interfaces\Controller;
use App\Services\StatisticService;
use Cache;

/**
 * Class StatisticController
 */
class StatisticController extends Controller
{
    private $statisticSvc;

    public function __construct(
      StatisticService $statisticSvc
    ) {
        $this->statisticSvc = $statisticSvc;
    }

    public function index()
    {
        // Get company statistic
        //Cache::forget('compantStats');
        $company_statistic = Cache::remember('compantStats', 60, function () {
            return $this->statisticSvc->getCompanyStatistic();
        });

        // Get pilot statistic
        $pilot_statistic = Cache::remember('pilotStats', 60, function () {
            return $this->statisticSvc->getPilotStatistic();
        });

        // Get activity statistic
        $activity_stats = Cache::remember('activityStats', 60, function () {
            return $this->statisticSvc->getActivityStatistic();
        });

        // Get airport statistic
        $airport_stats = Cache::remember('airportStats', 60, function () {
            return $this->statisticSvc->getAirportStatistic();
        });

        return view('statistics.index', [
                'company_statistic' => $company_statistic,
                'pilot_statistic'   => $pilot_statistic,
                'activity_stats'    => $activity_stats,
                'airport_stats'     => $airport_stats,
            ]);
    }
}
