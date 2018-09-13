const leaflet = require('leaflet');

import draw_base_map from './base_map'

import {ACTUAL_ROUTE_COLOR, CIRCLE_COLOR, PLAN_ROUTE_COLOR} from './config'

/**
 * Show each point as a marker
 * 
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
        render_elem: 'map',
        selected_airport: null,
    }, opts);
    
    console.log(opts);
    
    let airportPointsLayer = null;
    let routeLayer = null;
    let map = null;
    
    /**
     * Show some popup text when a feature is hovered
     * Load route from selected departure airport
     * 
     * @param feature
     * @param layer
     */
    const onFeaturePointClick = (feature, layer) => {
        let popup_html = '';
        if (feature.properties && feature.properties.popup) {
            popup_html += feature.properties.popup
        }
    
        layer.bindPopup(popup_html);
        layer.on('mouseover', function (e) {
            this.openPopup();
        });
        layer.on('mouseout', function (e) {
            this.closePopup();
        });
        
        layer.on('click', function (e) {
            if (routeLayer !== null) {
                map.removeLayer(routeLayer);
            }
            loadRoute($('#airline').val(), e.target.feature.properties.name);
        });
    };
    
    /**
     * Load all airports flown by the selected airline
     * 
     * @param airlineId
     */
    const loadAirportPoints = (airlineId) => {
        console.log('Retrieving airports for airline ' + airlineId);
        
        let airports = $.ajax({
            url: '/api/flights/airports/' + airlineId,
            dataType: 'json',
            error: console.log
        });
        
        $.when(airports).done(airportGeoJson => {
            airportPointsLayer = leaflet.geoJSON(airportGeoJson, {
                onEachFeature: onFeaturePointClick,
                pointToLayer: pointToLayer,
                style: {
                    'color': PLAN_ROUTE_COLOR,
                    'weight': 3,
                    'opacity': 0.65,
                },
            });
        
            airportPointsLayer.addTo(map);
        });
    };
    
    /**
     * Load all routes from a departure point
     * 
     * @param airlineId
     */
    const loadRoute = (airlineId, departure) => {
        console.log('Retrieving route from ' + departure + ', airline: ' + airlineId);
        
        let routes = $.ajax({
            url: '/api/flights/destination/' + airlineId + '/' + departure,
            dataType: 'json',
            error: console.log
        }); 
        
        $.when(routes).done(routeGeoJson => {
            if (routeLayer !== null) {
                map.removeLayer(routeLayer);
            }
            routeLayer = leaflet.geodesic([], {
                weight: 1.2,
                opacity: 0.6,
                color: PLAN_ROUTE_COLOR,
                steps: 50,
                wrap: false,
            }).addTo(map);
            
            routeLayer.geoJson(routeGeoJson);
            
            try {
                map.fitBounds(routeLayer.getBounds())
            } catch (e) {
                console.log(e)
            }
        });
    };
    
    map = draw_base_map(opts);
    map.once('focus', function() { map.scrollWheelZoom.enable(); });
    
    // Redraw map when airline selection changed
    let airlineId = $('#airline').val();
    $('#airline').change(function() {
        map.removeLayer(airportPointsLayer);
        map.removeLayer(routeLayer);
        
        loadAirportPoints($('#airline').val());  
        if (opts.selected_airport !== null) {
            loadRoute($('#airline').val(), opts.selected_airport);    
        }
    });
    
    // Initial data load when first time draw
    loadAirportPoints(airlineId);
    if (opts.selected_airport !== null) {
        loadRoute(airlineId, opts.selected_airport);
    }
};
