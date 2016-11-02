<?php
/*
Plugin Name: UI Leaflet Integration
Plugin URI: http://f2w.de/ui-leaflet-integration
Description: The most less excessive Leaflet map integration. Keeping it simple with a shortcode (and in the future, a quick address lookup + shortcode generator interface). Uses the latest Leaflet libary (1.0-rc1).
Tags: map, maps, Leaflet, OpenStreetMap, OSM, opendata, open data, location, geo, geocoding, geolocation, mapnik, mapquest, OpenLayers, mapping, coordinates, geocoding, geotagging, latitude, longitude, position, google maps, googlemaps, gmaps, google map, wms, web map service, marker, layer, multiple markers, karte, geographic, map short code, custom marker text
Version: 0.3
Author: Fabian Wolf
Author URI: http://usability-idealist.de/
License: GNU GPL v2
Requires at least: 4.1
Tested up to: 4.6
*/

// init

// includes
require_once( plugin_dir_path(__FILE__ ) . 'leaflet-map.class.php');

// for future versions
if( file_exists( plugin_dir_path(__FILE__ ) . 'admin.class.php' ) ) {
	require_once( plugin_dir_path(__FILE__ ) . 'admin.class.php' );
}

// main
add_action( 'plugins_loaded', array( '_ui_LeafletIntegration', 'init' ) );

if( class_exists( '_ui_LeafletAdmin' ) ) {
	//add_action( 'admin_init', array( '_ui_LeafletAdmin', 'init' ) );
	_ui_LeafletAdmin::init();
}
