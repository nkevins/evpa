<?php

namespace App\Listeners;

use App\Events\UserStatsChanged;
use App\Interfaces\Listener;
use App\Models\RankHistory;

/**
 * Class PilotPromotionListener
 */
class PilotPromotionListener extends Listener
{
    /**
     * Store rank promotion into history table
     *
     * @param UserStatsChanged $event
     */
    public function handle(UserStatsChanged $event): void
    {
        if ($event->stat_name == 'rank') {
            $history = new RankHistory();
            $history->user_id = $event->user->id;
            $history->old_rank_id = $event->old_value->id;
            $history->new_rank_id = $event->user->rank->id;
            $history->save();
        }
    }
}
