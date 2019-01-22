<?php
/**
 * Insert leaflet map plus options into post (or anywhere else)
 * 
 * @version 0.7
 */
 
class _ui_LeafletIntegration extends _ui_LeafletBase {
	public $pluginPrefix = 'ui_leaflet_',
		$pluginPath = '',
		$pluginURL = '',
		$pluginVersion = '0.7';
		
	
	public static function init() {
		new self();
	}
	
	function __construct() {
		$this->_setup();
		
		add_shortcode( 'ui_leaflet_map', array( $this, 'shortcode_map' ) );
		if( !empty( $this->config['enable_map_shortcode'] ) ) {
			add_shortcode('map', array( $this, 'shortcode_map' ) );
		}
		
		add_shortcode( 'ui_leaflet_marker', array( $this, 'shortcode_marker' ) );
		if( !empty( $this->config['enable_marker_shortcode'] ) ) {
			add_shortcode('marker', array( $this, 'shortcode_map' ) );
		}
		
		add_action( 'wp', array( $this, 'preload_assets' ) );
		
		add_action( 'wp_enqueue_scripts', array( $this, 'init_assets' ) );
		
		
	}
	
	/**
	 * Test if current post contains one of "our" shortcodes and if so, load assets
	 *
	 * Struct:
	 * - Check in preload_assets if shortcodes are being used
	 * - if so, use add_action( 'wp_enqueue_scripts + init AND load_assets
	 * - done!
	 */
		
	function preload_assets() {
		$post_id = parent::get_post_id();
		
		//new __debug( $post_id, __METHOD__ );
		
		if( empty( $post_id ) ) {
			global $post;
			
			if( !empty( $post ) && isset( $post->ID) ) {
				$post_id = $post->ID;
			}
		}
		
		//new __debug( $post_id, __METHOD__ );
		
		if( !empty( $post_id ) ) {
			$current_post = get_post( $post_id );
			$arrTargets = array();
			
			//new __debug( $current_post, 'current_post - ' . __METHOD__ );
			//$current_post->post_content
			$post_meta = get_metadata( 'post', $post_id );
			
			//new __debug( $post_meta, 'post meta - ' . __METHOD__ );
			
			if( !empty( $post_meta ) ) {
				$arrTargets = $post_meta;
			}
			
			$arrTargets['post_content'] = $current_post->post_content;
			
			
			$arrMethods = $this->_get_methods_by( 'prefix', 'shortcode_' );
		
			//new __debug( $arrTargets, 'targets - ' . __METHOD__ );
			
			if( !empty( $arrMethods ) ) {
				foreach( $arrMethods as $strMethod ) { // avoid clashes with the sks helper shortcodes
					$strShortcode = str_replace( 'shortcode_', '', $strMethod );
					
					
					foreach( $arrTargets as $single_target ) { // cycle through possible DIFFERENT strings (what we search for is not necessarly contained in the regular post_content)
						
						if( is_array( $single_target ) ) {
							$strContentTarget = reset( $single_target );
						} else {
							$strContentTarget = $single_target;
						}
					
					
						if( strpos( $strContentTarget, '[' . $strShortcode ) !== false ) {
							$arrDetectedShortcode[ $strShortcode ] = 1;
						}
					}
			
				}
			}
				
		}
		
		//new __debug( $arrDetectedShortcode, 'detected shortcodes - ' . __METHOD__ );

		
		if( !empty( $arrDetectedShortcode ) ) { // load assets
		
			add_action( 'wp_enqueue_scripts', array( $this, 'init_assets' ) );
			add_action( 'wp_enqueue_scripts', array( $this, 'load_assets' ) );
		}
	}
	
	function init_assets() {
		/**
		 * Change the version number (string!) to load a different version of the leaflet map library, eg. for backward compatiblity.
		 * @hook ui_leaflet_load_leaflet_version
		 */
		$leaflet_version = apply_filters( $this->pluginPrefix . 'load_leaflet_version', '0.7.7' );
		$leaflet_version = apply_filters( $this->pluginPrefix . 'load_leaflet_version', '1.0.1' );
		$leaflet_version = apply_filters( $this->pluginPrefix . 'load_leaflet_version', '1.2' );
		$leaflet_version = apply_filters( $this->pluginPrefix . 'load_leaflet_version', '1.3' );
		$leaflet_version = apply_filters( $this->pluginPrefix . 'load_leaflet_version', '1.3.3' );
		
		
		$leaflet_js_url = apply_filters( $this->pluginPrefix . 'js_url', trailingslashit( $this->pluginURL ). "assets/leaflet/$leaflet_version/leaflet.js");
		$leaflet_css_url = apply_filters( $this->pluginPrefix . 'css_url', trailingslashit( $this->pluginURL ) . "assets/leaflet/$leaflet_version/leaflet.css");
		
		
		$load_in_footer = apply_filters( $this->pluginPrefix . 'load_in_footer', true );
		
		wp_register_script( $this->pluginPrefix .'js', $leaflet_js_url, null, $leaflet_version, $load_in_footer );	
		wp_register_script( $this->pluginPrefix . 'plugin', trailingslashit( $this->pluginURL ). 'assets/plugin.js', array( 'jquery', $this->pluginPrefix . 'js' ),  $this->pluginVersion, $load_in_footer );
		
		/**
		 * NOTE: DO _NOT_ attempt to load the CSS in the footer - it will fuck up the map display!
		 * FIXME: Currently there is no guarantee whether the CSS file is loaded before or AFTER the JS file. BUT: It MUST always be loaded before, else we get visual chaos!
		 */
		
		
		wp_register_style( $this->pluginPrefix .'css', $leaflet_css_url, null, $leaflet_version );
		wp_register_style( $this->pluginPrefix .'plugin', trailingslashit( $this->pluginURL ) . 'assets/plugin.css', array( $this->pluginPrefix . 'css' ), $this->pluginVersion, false );
		
		// dont load CSS in footer
		
		$this->load_assets('css');
		
		
	}
	
	function load_assets( $type = 'all' ) {
		if( is_admin() ) { // avoid loading in the backend
			return;
		}
		
		if( empty( $type ) ) {
			$type = 'all';
		}
		
		if( $type == 'all' || $type == 'js' || $type == 'script' ) {
			
			// avoid double enqueuing
			/*if( $this->is_enqueued( 'script', $this->pluginPrefix . 'js' ) ) {
				wp_enqueue_script( $this->pluginPrefix .'js' );
			}*/
			
			//if( $this->is_enqueued( 'script', $this->pluginPrefix . 'plugin' ) == false ) {
			if( !wp_script_is( $this->pluginPrefix . 'js', 'enqueued' ) ) {
				//$this->enqueue_script( $this->pluginPrefix . 'plugin' );
				wp_enqueue_script( $this->pluginPrefix .'plugin' );
			}
		}
		
		if( $type == 'all' || $type == 'css' || $type == 'style' ) {
			
			// avoid double enqueuing
			if( !wp_style_is( $this->pluginPrefix . 'css', 'enqueued' ) ) {
			//if( $this->is_enqueued( 'style', $this->pluginPrefix . 'plugin' ) == false ) {
			
				wp_enqueue_style( $this->pluginPrefix .'css' );
				//$this->enqueue_style( $this->pluginPrefix . 'plugin' );
			}
		}
	}
	
	function _setup() {
		/**
		 * @hook array ui_leaflet_get_options	Add your own here, eg. by dropping them in the functions.php of your current theme ;)
		 */
		$arrOptions = apply_filters( $this->pluginPrefix . 'get_options', get_option( $this->pluginPrefix, array() ) );
		
		// for the future
		//$arrOptions = get_option( $this->pluginPrefix . 'settings', array() );
		
		if( !empty( $arrOptions ) && is_array( $arrOptions ) ) {
			$this->config = wp_parse_args( $this->_get_default_params(), $arrOptions );
		} else {
			$this->config = $this->_get_default_params();
		}
		
			
		
		$this->pluginPath = plugin_dir_path( __FILE__ );
		$this->pluginURL = plugin_dir_url( __FILE__ );
		
		if( defined( '_UI_LEAFLET_MAP_URL' ) ) {
			$this->pluginURL = _UI_LEAFLET_MAP_URL;
		}
		
		if( defined( '_UI_LEAFLET_MAP_PATH' ) ) {
			$this->pluginPath = _UI_LEAFLET_MAP_PATH;
		}
		
		add_action('wp_footer', array( $this, 'reset_queue' ), 9999 );
		
	}
	
	function reset_queue() {
		
	}
	
	/**
	 * NOTE: See @link https://josm.openstreetmap.de/wiki/Maps for a list of available tile server services 
	 */

	protected function _get_default_params() {
		return array(
			'enable_map_shortcode' => false,
			'enable_marker_shortcode' => false,
			'tile_server' => 'http://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png',
			'map_height' => '300px', /* default height */
		);
	}
	

	/**
	 * NOTE: Future multiple marker shortcode (should reside either IN the map shortcode, or point to its ID (use @param string $id in @method shortcode_map)
	 * 
	 * @param array $attr:
	 * @param string $lat|latitude		Latitude. Required.
	 * @param string $long|longitude	Longitude. Required.
	 * @param string $position			Format: "$longitude, $latitude". Alternative to using the longitude and latitude parameters.
	 * @param string $text				Content of the popup. If left empty, there will be none. Alternative: Uses the @param $content as a way more powerful replacement
	 * @param string $title				Title of the popup. 
	 * @param string $map_id|mapid|map	ID of the map. If none is set, the marker is assigned to the container map (if there is one). If there is none, the marker shortcode is removed.
	 * 
	 * @since 0.2
	 */
	
	function shortcode_marker( $attr = array(), $content = '' ) {
		$return = '';
		
		extract( shortcode_atts( array(
			'lat' => '',
			'long' => '', /** aliases */
				'latitude' => '',
				'longitude' => '',
				'position' => '',
			'text' => '',
			'title' => '',
			'map' => '', /* aliases */
				'map_id' => '',
				'mapid' => '',
			'routing' => '', /* aliases */
				'add_routing' => '',
				'routing_link' => '',
		), $attr ), EXTR_SKIP );
		
		
		
		if( !empty( $text) || !empty( $content ) || !empty( $map ) ) {
			
		}
		
		return $return;
	}
	
	function get_tile_server( $handle = '' ) {
		$return = '';
		
		/**
		 * List from @link https://josm.openstreetmap.de/wiki/Maps
		 */
		
		switch( $handle ) {
			case 'default':
			case 'standard':
			
				$return = $this->config['tile_server'];
				break;
			case 'mapnik_bw':
			case 'mapnik_black_white':
			case 'osm_bw':
			case 'osmbw':
			case 'black_white':
			case 'bw':
					// 'http://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png',
				$return = 'https://tiles.wmflabs.org/bw-mapnik/{z}/{x}/{y}.png';
				break;
			case 'nolabels':
			case 'no_labels':
			case 'mapnik_no_labels':
			case 'osm_no_labels':
			case 'osm_blank':
				$return = 'http://www.toolserver.org/tiles/osm-no-labels/{z}/{x}/{y}.png';
				break;
			
			case 'mapnik':
			case 'mapnick':
			default:
				$return = 'http://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png';
				break;
				
			case 'skobbler':
				$return = '';
				break;
		}
		
		return $return;
	}
	
	/**
	 * Shortcode for OSM / Leaflet map integration
	 * Uses custom meta / ACF if available.
	 * 
	 * @param string $use_field			Name of the custom meta field to fetch data from. Optional.
	 * @param string $lat|latitude		Latitude. Required.
	 * @param string $long|longitude	Longitude. Required.
	 * @param string $position			Format: "$longitude, $latitude". Alternative to using the longitude and latitude parameters.
	 * @param int $zoom					Zoom level. Optional.
	 * @param string $routing			Possible routing apps: google (Google Maps)
	 * 
	 * 48.1372568,11.5759285 <= fischbrunnen = base coordinates
	 * lat=48.13733&lon=11.57599 (acc. to OSM) => http://openstreetmap.de/karte.html?zoom=18&lat=48.13733&lon=11.57599&layers=000BTT
	 */
	
	function shortcode_map( $attr = array(), $content = '' ) {
		$return = '';
		$strMarker = '';
		
		static $map_count;
		$map_count++;
		
		$params = shortcode_atts( array(
			'use_field' => '',
			'latitude' => '48.1372568',
			'longitude' => '11.5759285',
			'position' => '48.1372568, 11.5759285',
			'zoom' => 16,
			'marker' => '', /*'48.1372568, 11.5759285' => position; use $content as text: h1 - h6 = title, rest = text */
			'routing' => '',
			'route_service' => '',
			'layer' => 'default',
			'layer_api_key' => '',
			'class' => 'ui-leaflet-map',
			'marker_class' => 'ui-leaflet-marker',
			'id' => 'ui-leaflet-map-id-%s',
			'marker_id' => 'ui-leaflet-marker-%s',
			'height' => $this->config['map_height'],
		), $attr );
		
		
		//new __debug( $params, 'parsed params: ' . __METHOD__ );
		//new __debug( $attr, 'original params: ' . __METHOD__ );
		
		extract( $params, EXTR_SKIP );
		
		
		// turn lat + long into position
		
	
		if( empty( $longitude ) && empty( $latitude ) && !empty( $position ) && strpos( $position, ',') !== false ) {
			$x = array_map( 'trim', explode(',', $position ) );
			
			if( $x[0] != $latitude || $x[1] != $longitude ) {
				$latitude = trim( $x[0] );
				$longitude = trim( $x[1] );
			}
		}
		
		if( !empty( $longitude ) && !empty( $latitude ) ) {
			
			$strMapID = sprintf( $id, $map_count );
			$strClass = $class;
			
			if( strpos( $class, '%' ) !== false ) {
				$strClass = sprintf( $class, $map_count );
			}
			
			
			
			$arrMapConfig = array(
				'latitude' => $latitude,
				'longitude' => $longitude,
				'zoom' => $zoom,
			);
			
			if( !empty( $height ) ) {
				$arrMapConfig['height'] = $height;
				
				$arrUnits = array( 'px', '%', 'pt', 'em', 'rem', 'vh');
				
				for( $n = 0; $n < sizeof( $arrUnits ) && empty( $hasUnit ) ; $n++ ) {
					if( strpos( $height, $arrUnits[ $n ] ) === false ) {
						$hasUnit = false;
					} else {
						$hasUnit = true;
					}
				}
				
				
				/*
				foreach( array( 'px', '%', 'pt', 'em', 'rem', 'vh') as $strUnit ) {
					if( strpos( $height, $strUnit ) === false ) {
						$hasNoUnit = true;
					} else {
						$hasNoUnit = false;
						break;
					}
				}*/
				
				if( empty( $hasUnit ) ) {
					if( $height > 100 ) {
						$arrMapConfig['height'] .= 'px';
					} else {
						$arrMapConfig['height'] .= '%';
					}
				}
			}
			
			
			$arrMapConfig['layer'] = $this->get_tile_server( $layer );
		}
					
		
		if( !empty( $longitude ) && !empty( $latitude ) ) {
			if( !empty( $content ) ) {
			
			
				$marker_latitude = $latitude;
				$marker_longitude = $longitude;
				
				// get position
				if( !empty( $marker ) && strpos( $marker, ',') !== false ) {
					$x2 = array_map( 'trim', explode( ',', $marker_position ) );
					
					$marker_latitude = $x2[0];
					$marker_longitude = $x2[1];	
				}
				
				/**
				 * NOTE: Fixes that nasty wpautop behaviour
				 */
			
				$strCleanContent = trim( str_replace(
					array( "<br />\n<h", "<br>\n<h" ),
					'<h',
					strip_tags( $content, '<h1><h2><h3><h4><h5><h6><a><strong><em><b><i><del><s><p><br><ul><ol><li><dl><dt><dd>' ) 
				) );
				
				/**
				 * Optionally append routing link
				 * 
				 * - google: http://maps.google.com/maps?saddr=Seestr.+11+13349+Berlin&daddr=Seestr.+38+13349+Berlin
				 * - ors / openroute / openrouteservice: https://www.openrouteservice.org/directions?n1=48.133088&n2=11.562892&n3=15&a=48.137236,11.576181,48.131265,11.54922&b=0&c=0&k1=en-US&k2=km
				 * => https://www.openrouteservice.org/directions?n1=48.137257&n2=11.575929&n3=15&a=48.137257,11.575929,null,null&b=0&c=0&k1=en-US&k2=km
				 * - graphhopper / gh: https://graphhopper.com/maps/?point=Theresienwiese%2C%2080336%2C%20M%C3%BCnchen%2C%20Deutschland&point=Fischbrunnen%2C%2080331%2C%20M%C3%BCnchen%2C%20Deutschland&locale=de-DE&vehicle=car&weighting=fastest&elevation=true&use_miles=false&layer=Omniscale
				 * 
				 * https://graphhopper.com/maps/?point=48.1372568,11.5759285&use_miles=false&vehicle=car
				 *
				 * TODO: Turn this into something dynamic, preferable with a filter (array)
				 */
			
				if( !empty( $routing ) || !empty( $route_service)  ) {
					$strRoutingService = ( !empty( $routing ) ? $routing : $route_service );
					
					
					switch( $strRoutingService ) {
						case 'google':
						case 'true':
						case 'yes':
							$strRoutingURL = 'http://maps.google.com/maps?saddr=' . $marker_latitude . ',' . $marker_longitude; // lat, long
							$strRoutingTitle = 'Google Maps';
							break;
						case 'ors':
						case 'orsm':
						case 'openroute':
						case 'openrouteservice':
							// https://www.openrouteservice.org/directions?n1=48.1372568&n2=11.5759285&n3=15&a=48.1372568,11.5759285&b=0&c=0 <= 48.1372568, 11.5759285'
							$strRoutingURL = 'https://www.openrouteservice.org/directions?n1=' . $marker_latitude . '&n2='. $marker_longitude .'&n3=' . ( !empty( $zoom ) ? $zoom : 15 ) . '&a='. $marker_latitude . ',' . $marker_longitude .',null,null&b=0&c=0&k1=en-US&k2=km';
							$strRoutingTitle = 'Open Route Service';
							
							
							break;
						case 'gh':
						case 'graph':
						case 'graphhopper':
						case 'graphhoper':
							break;
					}
					
					$strRoutingText = sprintf( __('Open in %s', '_ui-leaflet-integration'), $strRoutingTitle );
					if( !empty( $routing_text ) || !empty( $route_service_text ) ) {
						$strRoutingText = ( !empty( $routing_text ) ? $routing_text : $route_service_text );
					}
					
					if( !empty( $strRoutingURL ) ) {
						$strCleanContent .= '<p class="ui-leaflet-routing-service"><a href="' . esc_url( $strRoutingURL ) . '">' . $strRoutingText . '</a></p>';
					}
				}
				
				
			
			
				$strMarkerID = sprintf( $marker_id, $map_count . '-1' );
				$strMarker = sprintf( '<script class="%s" id="%s" type="text/html">%s</script>', $marker_class, $strMarkerID, $strCleanContent );
				
				$arrMapConfig['marker_position'] = array(
					'longitude' => $marker_longitude,
					'latitude' => $marker_latitude,
				);
					
			}
			
			//new __debug( $arrMapConfig, 'map config: ' . __METHOD__ );
			
			//$strMapConfig = sprintf( '<script class="leaflet-config" id="%s" type="text/html">%s</script>', $strMapID . '-config', json_encode( $arrMapConfig ) );
			$strMapConfig = json_encode( $arrMapConfig, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT );
			
			
			$return = sprintf( '<div class="%s" id="%s" data-leaflet-config=\'%s\'>%s</div>', $strClass, $strMapID, $strMapConfig, $strMarker );
			
			$this->load_assets();
		}
	
		
		return $return;
	}
	
	/**
	 * NOTE: Preparation for 0.8+
	 */
	
	function get_routing_services() {
		$return = false;
		
		$arrKnownServices = array(
			'Google Maps' => array(
				'keywords' => array( 
					'google', 'yes', 'true' 
				),
				'url' => 'http://maps.google.com/maps?saddr=%marker_latitude%,%marker_longitude%',
				'max_zoom' => 15, /* defaults to 16 = OSM */
			),
			
			'Open Route Service' => array(
				'keywords' => array(
					'ors',
					'orsm',
					'openroute',
					'openrouteservice',
				),
				'url' => 'https://www.openrouteservice.org/directions?n1=%marker_latitude%&n2=%marker_longitude%&n3=%zoom%&a=%marker_latitude%,%marker_longitude%,null,null&b=0&c=0&k1=en-US&k2=km',
			),
						
			'GraphHopper' => array(
				'keywords' => array(
					'gh', 'graphhopper', 'graphopper', 'graphhoper',
				),
				'url' => '',
			),
		);
		
		$return = apply_filters( $this->pluginPrefix . 'get_routing_services', $arrKnownServices );
		
		return $return;
	}
	
}
