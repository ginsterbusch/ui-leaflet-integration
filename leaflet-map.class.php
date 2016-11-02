<?php
/**
 * Insert leaflet map plus options into post (or anywhere else)
 * 
 * @version 0.3
 */
 
class _ui_LeafletIntegration {
	public $pluginPrefix = 'ui_leaflet_',
		$pluginPath = '',
		$pluginURL = '',
		$pluginVersion = '0.3';
		
	
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
		
		$this->init_assets();	

	}
	
	function init_assets() {
		/**
		 * Change the version number (string!) to load a different version of the leaflet map library, eg. for backward compatiblity.
		 * @hook ui_leaflet_load_leaflet_version
		 */
		$leaflet_version = apply_filters( $this->pluginPrefix . 'load_leaflet_version', '0.7.7' );
		$leaflet_version = apply_filters( $this->pluginPrefix . 'load_leaflet_version', '1.0.1' );
		
		$leaflet_js_url = apply_filters( $this->pluginPrefix . 'js_url', trailingslashit( $this->pluginURL ). "assets/leaflet/$leaflet_version/leaflet.js");
		$leaflet_css_url = apply_filters( $this->pluginPrefix . 'css_url', trailingslashit( $this->pluginURL ) . "assets/leaflet/$leaflet_version/leaflet.css");
		
		
		$load_in_footer = apply_filters( $this->pluginPrefix . 'load_in_footer', true );
		
		/*
		new __debug( array(
			'leaflet_version' => $leaflet_version,
			'js_url' => $leaflet_js_url,
			'css_url' => $leaflet_css_url,
		), __CLASS__  );
		*/
		
		
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
	
	/**
	 * Wrapper / Alias for @method enqueue
	 */
	
	function enqueue_script( $handle = '' ) {
		$this->enqueue( 'script', $handle );
	}
	
	function enqueue_css( $handle = '' ) {
		$this->enqueue( 'style', $handle );	
	}
	
	function enqueue_style( $handle = '' ) {
		$this->enqueue( 'style', $handle );	
	}
	
	
	
	function enqueue( $type = 'script', $handle = '' ) {
		global $_ui_is_enqueued;
		
		
		if( !empty( $handle ) && !empty( $type ) && !is_admin() ) {
		/*	new __debug( array(
				'handle' => $handle,
				'type' => $type,
			), __METHOD__ );
		*/
			if( empty( $_ui_is_enqueued[ $type ][ $handle ] ) ) { // empty is better, because FALSE := empty, too!
				$_ui_is_enqueued[ $type ][ $handle ] = true;
				
				
				switch( $type ) {
					case 'style':
					case 'css':
					
						wp_enqueue_style( $handle );
						break;
					case 'script':
					case 'js':
						wp_enqueue_script( $handle );
						break;
				}
			}
		}
		
	}
	
	/**
	 * wp_style_is / wp_script_is works unreliable in the content / below wp_head
	 */
	
	function is_enqueued( $type = 'script', $handle = '' ) {
		global $_ui_is_enqueued;
		$return = false;
		
		if( !empty( $handle ) && !empty( $_ui_is_enqueued[ $type ][ $handle ] ) ) {
			
			$return = true;
		}
		
		//new __debug( array( 'return code' => ( $return ? 'true' : 'false' ) ), __METHOD__ );
		
		return $return;
	}
	
	function reset_queue() {
		global $_ui_is_enqueued;
		
		$_ui_is_enqueued = array(); // soft reset
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
				$this->enqueue_script( $this->pluginPrefix . 'plugin' );
				//wp_enqueue_script( $this->pluginPrefix .'plugin' );
			}
		}
		
		if( $type == 'all' || $type == 'css' || $type == 'style' ) {
			
			// avoid double enqueuing
			if( !wp_style_is( $this->pluginPrefix . 'css', 'enqueued' ) ) {
			//if( $this->is_enqueued( 'style', $this->pluginPrefix . 'plugin' ) == false ) {
			
				//wp_enqueue_style( $this->pluginPrefix .'css' );
				$this->enqueue_style( $this->pluginPrefix . 'plugin' );
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
		
		add_action('wp_footer', array( $this, 'reset_queue' ), 9999 );
		
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
}
