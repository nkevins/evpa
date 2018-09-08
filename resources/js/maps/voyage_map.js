const leaflet = require('leaflet');

import draw_base_map from './base_map'
import { addWMSLayer } from './helpers';

import {ACTUAL_ROUTE_COLOR, CIRCLE_COLOR, PLAN_ROUTE_COLOR} from './config'

/**
 * Show some popup text when a feature is clicked on
 * @param feature
 * @param layer
 */
export const onFeaturePointClick = (feature, layer) => {
    let popup_html = '';
    if (feature.properties && feature.properties.popup) {
        popup_html += feature.properties.popup
    }

    layer.bindPopup(popup_html)
};

/**
 * Show each point as a marker
 * @param feature
 * @param latlng
 * @returns {*}
 */
export const pointToLayer = (feature, latlng) => {
    return leaflet.circleMarker(latlng, {
        radius: 5,
        fillColor: CIRCLE_COLOR,
        color: '#000',
        weight: 1,
        opacity: 1,
        fillOpacity: 0.8
    })
}

/**
 *
 * @param opts
 * @private
 */
export default (opts) => {

    opts = Object.assign({
        routes: null,
        render_elem: 'map',
    }, opts);
    
    console.log(opts);
    
    let map = draw_base_map(opts);
    
    opts.routes.forEach(function(e) {
    
        let geodesicLayer = leaflet.geodesic([], {
            weight: 2,
            opacity: 0.9,
            color: PLAN_ROUTE_COLOR,
            steps: 10,
            wrap: false,
        }).addTo(map);
        
        geodesicLayer.geoJson(e.planned_route_line);
        
        try {
            map.fitBounds(geodesicLayer.getBounds())
        } catch (e) {
            console.log(e)
        }
        
        // Draw the route points after
        if (e.route_points !== null) {
            let route_points = leaflet.geoJSON(e.route_points, {
                onEachFeature: onFeaturePointClick,
                pointToLayer: pointToLayer,
                style: {
                    'color': PLAN_ROUTE_COLOR,
                    'weight': 3,
                    'opacity': 0.65,
                },
            });
        
            route_points.addTo(map);
        }
    });
};