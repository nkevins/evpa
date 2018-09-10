<?php

namespace App\Widgets;

use App\Interfaces\Widget;
use App\Repositories\PirepRepository;
use App\Services\GeoService;

/**
 * Show the voyage map in a view
 */
class VoyageMap extends Widget
{
    protected $config = [
        'height' => '300px',
        'width'  => '100%',
        'user'   => null,
    ];

    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function run()
    {
        $geoSvc = app(GeoService::class);
        $pirepRepo = app(PirepRepository::class);
        
        $voyageMapData = $pirepRepo->getVoyageMapData($this->config['user']);
        $map_features = $geoSvc->voyageGeoJson($voyageMapData);
        
        $center_coords = setting('acars.center_coords', '0,0');
        $center_coords = array_map(function ($c) {
            return (float) trim($c);
        }, explode(',', $center_coords));

        return view('widgets.voyage_map', [
            'config'       => $this->config,
            'map_features' => $map_features,
            'center'       => $center_coords,
            'zoom'         => setting('acars.default_zoom', 5),
        ]);
    }
}
