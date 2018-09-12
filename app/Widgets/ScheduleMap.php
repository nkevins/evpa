<?php

namespace App\Widgets;

use App\Interfaces\Widget;
use App\Repositories\AirlineRepository;

/**
 * Show the schedule map in a view
 */
class ScheduleMap extends Widget
{
    protected $config = [
        'height'           => '800px',
        'width'            => '100%',
        'selected_airport' => null,
    ];

    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function run()
    {
        $airlineRepo = app(AirlineRepository::class);

        $center_coords = setting('acars.center_coords', '0,0');
        $center_coords = array_map(function ($c) {
            return (float) trim($c);
        }, explode(',', $center_coords));

        return view('widgets.schedule_map', [
            'config'   => $this->config,
            'airlines' => $airlineRepo->selectBoxList(),
            'center'   => $center_coords,
            'zoom'     => 3,
        ]);
    }
}
