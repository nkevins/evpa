<?php

namespace App\Services;

use App\Interfaces\Service;
use App\Models\Enums\UserState;
use App\Models\Flight;
use App\Models\Pirep;
use App\Models\User;
use Carbon;
use DB;

/**
 * Class StatisticService
 */
class StatisticService extends Service
{
    /**
     * Get company statistic data
     *
     * @return array
     */
    public function getCompanyStatistic()
    {
        $company_statistic = [];
        $company_statistic['total_user'] = User::count();
        $company_statistic['active_percentage'] = round(User::where('state', UserState::ACTIVE)->count() / $company_statistic['total_user'] * 100, 2);
        $company_statistic['total_flights'] = Flight::count();
        $company_statistic['aircraft_usage'] = DB::table('subfleets')
                                                ->join('flight_subfleet', 'subfleets.id', '=', 'flight_subfleet.subfleet_id')
                                                ->groupBy('subfleets.name')
                                                ->select(DB::raw('subfleets.name, count(*) as count'))
                                                ->get();
        $company_statistic['max_distance'] = Flight::orderBy('distance', 'desc')->limit(1)->first();
        $company_statistic['min_distance'] = Flight::orderBy('distance', 'asc')->limit(1)->first();
        $company_statistic['max_duration'] = Flight::orderBy('flight_time', 'desc')->limit(1)->first();
        $company_statistic['min_duration'] = Flight::orderBy('flight_time', 'asc')->limit(1)->first();
        $company_statistic['schedule_count_omdb'] = Flight::where('dpt_airport_id', 'OMDB')->orWhere('arr_airport_id', 'OMDB')->count();
        $company_statistic['schedule_count_omdw'] = Flight::where('dpt_airport_id', 'OMDW')->orWhere('arr_airport_id', 'OMDW')->count();
        $company_statistic['last_updated_date'] = Carbon::now();

        return $company_statistic;
    }

    /**
     * Get pilot statistic data
     *
     * @return array
     */
    public function getPilotStatistic()
    {
        $pilot_statistic = [];
        $pilot_statistic['max_no_flights'] = User::where('state', UserState::ACTIVE)->orderBy('flights', 'desc')->first();
        $pilot_statistic['min_no_flights'] = User::where('state', UserState::ACTIVE)->orderBy('flights', 'asc')->first();
        $pilot_statistic['max_flight_time'] = User::where('state', UserState::ACTIVE)->orderBy('flight_time', 'desc')->first();
        $pilot_statistic['min_flight_time'] = User::where('state', UserState::ACTIVE)->orderBy('flight_time', 'asc')->first();
        $pilot_statistic['max_distance'] = DB::table('users')
                                            ->join('pireps', 'users.id', '=', 'pireps.user_id')
                                            ->groupBy('users.name')
                                            ->select(DB::raw('users.name, sum(distance) as distance'))
                                            ->orderBy(DB::raw('sum(distance)'), 'desc')
                                            ->limit(1)
                                            ->first();
        $pilot_statistic['min_distance'] = DB::table('users')
                                            ->join('pireps', 'users.id', '=', 'pireps.user_id')
                                            ->groupBy('users.name')
                                            ->select(DB::raw('users.name, sum(distance) as distance'))
                                            ->orderBy(DB::raw('sum(distance)'), 'asc')
                                            ->limit(1)
                                            ->first();
        $pilot_statistic['last_pirep'] = Pirep::with('user')->orderBy('created_at', 'desc')->limit(1)->first();
        $pilot_statistic['all_hours'] = Pirep::sum('flight_time');
        $pilot_statistic['all_distance'] = Pirep::sum('distance');
        $pilot_statistic['avg_hours'] = Pirep::avg('flight_time');
        $pilot_statistic['avg_distance'] = Pirep::avg('distance');
        $pilot_statistic['min_td'] = Pirep::with('user')->orderBy('landing_rate', 'desc')->limit(1)->first();
        $pilot_statistic['max_td'] = Pirep::with('user')->orderBy('landing_rate', 'asc')->limit(1)->first();
        $pilot_statistic['avg_td'] = Pirep::avg('landing_rate');
        $pilot_statistic['min_last_td'] = Pirep::with('user')->where('created_at', '>=', Carbon::now()->subDays(30))->orderBy('landing_rate', 'desc')->limit(1)->first();
        $pilot_statistic['max_last_td'] = Pirep::with('user')->where('created_at', '>=', Carbon::now()->subDays(30))->orderBy('landing_rate', 'asc')->limit(1)->first();

        return $pilot_statistic;
    }

    /**
     * Get activity statistic data
     *
     * @return array
     */
    public function getActivityStatistic()
    {
        $activity_stats = [];
        $activity_stats['activity'] = DB::table('pireps')
                                        ->select(DB::raw('year(submitted_at) as year, month(submitted_at) as month, count(*) as count, 
                                            sum(distance) as ttl_distance, avg(distance) as avg_distance, sum(flight_time) as ttl_time'))
                                        ->where('state', '2')
                                        ->groupBy(DB::raw('year(submitted_at), month(submitted_at)'))
                                        ->orderBy(DB::raw('month(submitted_at)'), 'asc')
                                        ->orderBy(DB::raw('year(submitted_at)'), 'asc')
                                        ->get();
        $i = 0;
        foreach ($activity_stats['activity'] as $aa) {
            if ($i == 0) {
                $aa->count_percentage = null;
                $aa->ttl_distance_percentage = null;
                $aa->avg_distance_percentage = null;
                $aa->ttl_time_percentage = null;
            } else {
                $prev_aa = $activity_stats['activity'][$i - 1];
                $aa->count_percentage = round(($aa->count - $prev_aa->count) / $prev_aa->count * 100, 2);
                $aa->ttl_distance_percentage = round(($aa->ttl_distance - $prev_aa->ttl_distance) / $prev_aa->ttl_distance * 100, 2);
                $aa->avg_distance_percentage = round(($aa->avg_distance - $prev_aa->avg_distance) / $prev_aa->avg_distance * 100, 2);
                $aa->ttl_time_percentage = round(($aa->ttl_time - $prev_aa->ttl_time) / $prev_aa->ttl_time * 100, 2);
            }
            $i++;
        }

        $activity_stats['td'] = DB::table('pireps')
                                        ->select(DB::raw('year(submitted_at) as year, month(submitted_at) as month, count(*) as count, 
                                            max(landing_rate) as max_td, min(landing_rate) as min_td'))
                                        ->where('state', '2')
                                        ->groupBy(DB::raw('year(submitted_at), month(submitted_at)'))
                                        ->orderBy(DB::raw('month(submitted_at)'), 'asc')
                                        ->orderBy(DB::raw('year(submitted_at)'), 'asc')
                                        ->get();
        $i = 0;
        foreach ($activity_stats['td'] as $atd) {
            if ($i == 0) {
                $atd->count_percentage = null;
            } else {
                $prev_atd = $activity_stats['td'][$i - 1];
                $atd->count_percentage = round(($atd->count - $prev_atd->count) / $prev_atd->count * 100, 2);
            }

            $atd->max_td_name = DB::table('pireps')
                                    ->join('users', 'users.id', 'pireps.user_id')
                                    ->where('pireps.state', '2')
                                    ->where(DB::raw('year(submitted_at)'), $atd->year)
                                    ->where(DB::raw('month(submitted_at)'), $atd->month)
                                    ->select('users.name')
                                    ->orderBy('landing_rate', 'asc')
                                    ->orderBy('submitted_at', 'asc')
                                    ->limit(1)
                                    ->first()->name;
            $atd->min_td_name = DB::table('pireps')
                                    ->join('users', 'users.id', 'pireps.user_id')
                                    ->where('pireps.state', '2')
                                    ->where(DB::raw('year(submitted_at)'), $atd->year)
                                    ->where(DB::raw('month(submitted_at)'), $atd->month)
                                    ->select('users.name')
                                    ->orderBy('landing_rate', 'desc')
                                    ->orderBy('submitted_at', 'asc')
                                    ->limit(1)
                                    ->first()->name;
            $i++;
        }

        return $activity_stats;
    }

    /**
     * Get airport statistic data
     *
     * @return array
     */
    public function getAirportStatistic()
    {
        $airport_stats = [];
        $airport_stats['top_departure'] = DB::table('pireps')
                                            ->join('airports', 'airports.id', 'pireps.dpt_airport_id')
                                            ->groupBy(DB::raw('pireps.dpt_airport_id, airports.name'))
                                            ->orderBy(DB::raw('count(airports.id)'), 'desc')
                                            ->select(DB::raw('count(airports.id) as count, pireps.dpt_airport_id, airports.name'))
                                            ->limit(10)
                                            ->get();
        $airport_stats['top_destination'] = DB::table('pireps')
                                            ->join('airports', 'airports.id', 'pireps.arr_airport_id')
                                            ->groupBy(DB::raw('pireps.arr_airport_id, airports.name'))
                                            ->orderBy(DB::raw('count(airports.id)'), 'desc')
                                            ->select(DB::raw('count(airports.id) as count, pireps.arr_airport_id, airports.name'))
                                            ->limit(10)
                                            ->get();
        $airport_stats['top_route'] = DB::table('pireps')
                                            ->join('airports as dpt', 'dpt.id', 'pireps.dpt_airport_id')
                                            ->join('airports as arr', 'arr.id', 'pireps.arr_airport_id')
                                            ->groupBy(DB::raw('pireps.dpt_airport_id, dpt.name, pireps.arr_airport_id, arr.name'))
                                            ->orderBy(DB::raw('count(*)'), 'desc')
                                            ->select(DB::raw('count(*) as count, pireps.dpt_airport_id as dpt_icao, dpt.name as dpt_name, pireps.arr_airport_id as arr_icao, arr.name as arr_name'))
                                            ->limit(10)
                                            ->get();

        return $airport_stats;
    }
}
