<?php
/*
Plugin Name: UI Leaflet Integration
Plugin URI: http://f2w.de/ui-leaflet-integration
Description: The most less excessive Leaflet map integration. Keeping it simple with a shortcode (and in the future, a quick address lookup + shortcode generator interface). Uses the latest Leaflet libary (1.3). Also compatible with ClassicPress.
Tags: map, maps, Leaflet, OpenStreetMap, OSM, opendata, open data, location, geo, geocoding, geolocation, mapnik, mapquest, OpenLayers, mapping, coordinates, geocoding, geotagging, latitude, longitude, position, google maps, googlemaps, gmaps, google map, wms, web map service, marker, layer, multiple markers, karte, geographic, map short code, custom marker text, classicpress
Version: 0.7
Author: Fabian Wolf
Author URI: http://usability-idealist.de/
License: GNU GPL v2
Requires at least: 4.1
Tested up to: 5.0
*/

// init

define( '_UI_LEAFLET_MAP_PATH', plugin_dir_path( __FILE__ ) );
define( '_UI_LEAFLET_MAP_URL', plugin_dir_url( __FILE__ ) );

// includes
require_once( plugin_dir_path(__FILE__ ) . 'includes/base.class.php');


require_once( plugin_dir_path(__FILE__ ) . 'includes/leaflet-map.class.php');

// for future versions
if( file_exists( plugin_dir_path(__FILE__ ) . 'includes/admin.class.php' ) ) {
	require_once( plugin_dir_path(__FILE__ ) . 'includes/admin.class.php' );
}

// main
add_action( 'init', array( '_ui_LeafletIntegration', 'init' ) );

//if( class_exists( '_ui_LeafletAdmin' ) ) {
	//add_action( 'admin_init', array( '_ui_LeafletAdmin', 'init' ) );
	//_ui_LeafletAdmin::init();
//}
