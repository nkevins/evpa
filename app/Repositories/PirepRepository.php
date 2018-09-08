<?php

namespace App\Repositories;

use App\Interfaces\Repository;
use App\Models\Enums\PirepState;
use App\Models\Pirep;
use App\Models\User;
use Carbon;
use DB;

/**
 * Class PirepRepository
 */
class PirepRepository extends Repository
{
    protected $fieldSearchable = [
        'user_id',
        'status',
        'state',
    ];

    /**
     * @return string
     */
    public function model()
    {
        return Pirep::class;
    }

    /**
     * Get all the pending reports in order. Returns the Pirep
     * model but you still need to call ->all() or ->paginate()
     *
     * @param User|null $user
     *
     * @return Pirep
     */
    public function getPending(User $user = null)
    {
        $where = [];
        if ($user !== null) {
            $where['user_id'] = $user->id;
        }

        $pireps = $this->orderBy('created_at', 'desc')->findWhere($where)->all();

        return $pireps;
    }

    /**
     * Number of PIREPs that are pending
     *
     * @param User|null $user
     *
     * @return mixed
     */
    public function getPendingCount(User $user = null)
    {
        $where = [
            'state' => PirepState::PENDING,
        ];

        if ($user !== null) {
            $where['user_id'] = $user->id;
        }

        $pireps = $this->orderBy('created_at', 'desc')
            ->findWhere($where, ['id'])
            ->count();

        return $pireps;
    }
    
    /**
     * Get latest pirep with / without limit
     * 
     * @param User $user
     * @param int|null $limit
     * 
     * @return Pirep
     */
    public function getLastPirep(User $user, $limit = null)
    {
        $pireps = Pirep::where('user_id', $user->id)->with('airline')
                    ->whereNotIn('state', [
                        PirepState::CANCELLED,
                        PirepState::DRAFT,
                        PirepState::IN_PROGRESS,])
                    ->orderBy('created_at', 'desc')->limit($limit)->get();
        
        return $pireps;
    }
    
    /**
     * Get raw data airport points for voyage map
     * 
     * @param User $user
     * @return mix
     */
    public function getVoyageMapData(User $user)
    {
        $pireps = DB::select('select distinct da.icao as dep_icao, 
                    da.name as dep_name, da.lat as dep_lat, da.lon as dep_lon, 
                    aa.icao as des_icao, aa.name as des_name, aa.lat as des_lat, 
                    aa.lon as des_lon 
                    from pireps p inner join 
                    airports da on da.id = p.dpt_airport_id inner join 
                    airports aa on aa.id = p.arr_airport_id 
                    where user_id = ?', 
                    [$user->id]
                );
        
        return $pireps;
    }
    
    /**
     * 
     * Get user statistic
     * @param User
     * 
     * @return mixed
     */
    public function getUserPirepStatistic(User $user)
    {
        $stats = DB::table('pireps')
                        ->select(DB::raw('max(landing_rate) as max_rate, 
                                            min(landing_rate) as min_rate, 
                                            avg(landing_rate) as avg_rate,
                                            avg(distance) as avg_distance,
                                            avg(flight_time) as avg_flight_time'
                                        )) 
                        ->where('user_id', $user->id)
                        ->first();
                        
        $thisMonthFlight = DB::table('pireps')
                            ->where('user_id', $user->id)
                            ->where('created_at', '>=', Carbon::now()->startOfMonth())
                            ->count();
                            
        $lastMonthFlight = DB::table('pireps')
                            ->where('user_id', $user->id)
                            ->where('created_at', '>=', Carbon::now()->subMonth()->startOfMonth())
                            ->where('created_at', '<', Carbon::now()->startOfMonth())
                            ->count();
        
        $topDepartures = DB::table('pireps')
                            ->join('airports', 'pireps.dpt_airport_id', '=', 'airports.id')
                            ->where('user_id', $user->id)
                            ->groupBy('airports.id', 'airports.iata')
                            ->select(DB::raw('airports.id, airports.iata, airports.icao, count(*) as count'))
                            ->orderBy(DB::raw('count(*)'), 'desc')
                            ->limit(5)->get();
       
        $topDestination = DB::table('pireps')
                            ->join('airports', 'pireps.arr_airport_id', '=', 'airports.id')
                            ->where('user_id', $user->id)
                            ->groupBy('airports.id', 'airports.iata')
                            ->select(DB::raw('airports.id, airports.iata, airports.icao, count(*) as count'))
                            ->orderBy(DB::raw('count(*)'), 'desc')
                            ->limit(5)->get(); 
        
        return [
            'max_landing_rate' => is_null($stats->max_rate) ? 0 : $stats->max_rate,
            'min_landing_rate' => is_null($stats->min_rate) ? 0 : $stats->min_rate,
            'avg_landing_rate' => is_null($stats->avg_rate) ? 0 : $stats->avg_rate,
            'avg_distance'     => is_null($stats->avg_distance) ? 0 : $stats->avg_distance,
            'avg_flight_time'  => is_null($stats->avg_flight_time) ? 0 : $stats->avg_flight_time,
            'this_mth_flight'  => $thisMonthFlight,
            'last_mth_flight'  => $lastMonthFlight,
            'top_departure'    => $topDepartures,
            'top_destination'  => $topDestination
        ];
    }
}
