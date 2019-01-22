<?php
/**
 * UI Shortcode Assets Loader
 * On-demand loading of assets for shortcodes (ie. CSS and script files)
 * Also available as plugin (@link http://f2w.de/ui-shortcode-assets-loader)
 * 
 * @version 0.1
 * @licence GNU GPL v2
 * @author Fabian Wolf (@link http://usability-idealist.de/)
 */

class _ui_AssetsLoader {
	public $version = '0.1',
		$prefix = '_ui_assets_loader_';
		
	function __construct() {
	
	
	}
	
	function enqueue_asset( $params = array() ) {
		extract( wp_parse_args( $params, array(
			'handle' => '',
			'location' => 'wp_header',
			'type' => 'script', /* available types: script, style; in future: inline_script, inline_style */
			'src' => '',
			'url' => '', /* alias for src */
			'version' => get_bloginfo('version'),
			'deps' => array(),
			
		), EXTR_SKIP );
		
		if( !empty( $handle ) ) { // pre-registered script
					
		if( $type == 'script' && wp_script_is( 
		
	}


	function find_shortcode( $content = '', $shortcode = '' ) {
		$return = false;
		
		if( !empty( $shortcode ) && !empty( $content )  ) {
			if( ( $shortcode_position = strpos( $content, '[' . $shortcode . ']' ) ) !== false ) || ( $shortcode_position = strpos( $content, '[' . $shortcode ) ) !== false ) [
				$return = $shortcode_position;
			}
		}
		
		return $return;
	}
	

}
