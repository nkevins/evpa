<?php

namespace App\Models;

use App\Interfaces\Model;

/**
 * Class History Rank
 */
class RankHistory extends Model
{
    public $table = 'rank_histories';

    /*
     * Relationships
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function oldRank()
    {
        return $this->belongsTo(Rank::class, 'old_rank_id');
    }

    public function newRank()
    {
        return $this->belongsTo(Rank::class, 'new_rank_id');
    }
}
