<?php
/*
Plugin Name: UI Leaflet Integration
Plugin URI: https://github.com/ginsterbusch/ui-leaflet-integration
Description: The most less excessive Leaflet map integration. Keeping it simple with a shortcode (and in the future, a shortcode generator interface). Uses the latest Leaflet libary (1.6). Compatible with ClassicPress 1.x.
Tags: map, maps, Leaflet, OpenStreetMap, location, geocoding, geolocation, OpenLayers, geotagging, position, google maps, classicpress
Version: 0.9.3
Author: Fabian Wolf
Author URI: https://usability-idealist.net
License: GNU GPL v2
Requires at least: 4.1
Tested up to: 5.3
*/

// init

if( !defined( '_UI_LEAFLET_MAP_PATH' ) ) {
	define( '_UI_LEAFLET_MAP_PATH', plugin_dir_path( __FILE__ ) );
}

if( !defined( '_UI_LEAFLET_MAP_URL' ) ) {
	define( '_UI_LEAFLET_MAP_URL', plugin_dir_url( __FILE__ ) );
}

// includes
require_once( plugin_dir_path(__FILE__ ) . 'includes/base.class.php');

if( !class_exists( '_ui_LeafletIntegration' ) ) {
	require_once( plugin_dir_path(__FILE__ ) . 'includes/leaflet-map.class.php');
}
// helper functions lib
require_once( _UI_LEAFLET_MAP_PATH . 'includes/functions.php' );


// for future versions
if( file_exists( plugin_dir_path(__FILE__ ) . 'includes/admin.class.php' ) ) {
	require_once( plugin_dir_path(__FILE__ ) . 'includes/admin.class.php' );
}

// main
/**
 * TODO: Check if 'init' is really the right hook, esp. with the assets OD function (as it fires with the 'wp' hook)
 */
add_action( 'init', array( '_ui_LeafletIntegration', 'init' ) );


 
//if( class_exists( '_ui_LeafletAdmin' ) ) {
	//add_action( 'admin_init', array( '_ui_LeafletAdmin', 'init' ) );
	//_ui_LeafletAdmin::init();
//}
