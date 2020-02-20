<?php
/**
 * Template tags and various helper functions
 */
 
if( !function_exists( '_ui_leaflet_post_load_assets' ) ) {
 
	function _ui_leaflet_post_load_assets() {
		if( class_exists( '_ui_LeafletIntegration' ) ) {
		
			$that = new _ui_LeafletIntegration( false );
			$that->_post_load_assets();		
		}
	}
}
 
if( !function_exists( '_ui_leaflet_preload_assets' ) ) {
	function _ui_leaflet_preload_assets() {
		if( !defined( '_UI_LEAFLET_LOAD_ASSETS' ) ) {
			define( '_UI_LEAFLET_LOAD_ASSETS', true );
		}
	}
}
