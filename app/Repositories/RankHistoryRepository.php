<?php

namespace App\Repositories;

use App\Interfaces\Repository;
use App\Models\RankHistory;
use App\Repositories\Criteria\WhereCriteria;
use Illuminate\Http\Request;

/**
 * Class RankHistoryRepository
 */
class RankHistoryRepository extends Repository
{
    /**
     * @return string
     */
    public function model()
    {
        return RankHistory::class;
    }
    
    /**
     * Get latest 20 promotion list
     * 
     * @return mixed
     */
    public function getPromotionList()
    {
        $promotions = $this->with(['user', 'user.airline', 'oldRank', 'newRank'])
                        ->orderBy('created_at', 'desc')->paginate(20);
        
        return $promotions;
    }
}
