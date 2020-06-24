/**
 * Leaflet handler (theoretically requires no jQuery, but we use it anyway, just because ;) :P )
 * 
 * @version 0.7
 * 
 * Changelog:
 * 
 * v0.7:
 * - added custom options for zoom control and geocoder via global script object (ui_leaflet_extension_options)
 * 
 * v0.6.4:
 * - added customizable location detection error messages
 * 
 * v0.6.3:
 * - changed default marker HTML to use our own custom font icon set
 * 
 * v0.6.2:
 * - set zoom control position
 * 
 * v0.6.1:
 * - bugfix for search control position
 * 
 * v0.6:
 * - integration of geocoder / geosearch
 * 
 * v0.5:
 * - enhanced custom events with useful information
 * - event data always include the map ID
 * 
 * v0.4:
 * - added custom events
 */

jQuery( function() {
	// setup variables
	_ui_leaflet_maps = {}; // global object for further access
	_ui_leaflet_maps_position = {};
	
	// grab all maps
	
	if( jQuery('.ui-leaflet-map').length > 0 ) {
		jQuery('.ui-leaflet-map').each( function( iMapCount, elem)  { // i, elem
			// init map(s)
			var strMapID = jQuery( this ).attr('id');
			var config = jQuery( this ).data('leaflet-config');
			
		

			
			//console.log( 'config:', config, ' mapID: ', strMapID );
			
			if( typeof( strMapID ) != 'undefined' && strMapID != '' && typeof( config ) == 'object' ) {
				
				
				
				
				// fire custom init event
				jQuery( document ).trigger( '_ui_leaflet_map_init', {
					'map_id': strMapID,
					'config': config,
					'map_count': iMapCount,
				} ); //

				
				
				// init map
				// set default height - or override using the config
				
				
				jQuery(this).css( {'height': ( typeof( config.height ) != 'undefined' ? config.height : '250px' ), 'border':'1px solid #fc0', 'zindex':50 } );
				
				
				if( typeof(config.latitude ) != 'undefined' && typeof( config.longitude ) != 'undefined' ) {
					var map_zoomlevel = ( typeof( config.zoom ) != 'undefined' ? config.zoom : 16 );
					
					//console.log( 'zoomlevel:', map_zoomlevel ); 
					
					var	map_layer = ( typeof( config.layer ) != 'undefined' ? config.layer : 'https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png' );
					
					// configure zoom Control
					var _use_zoom_control = true;
					if( typeof( config.zoom_position ) != 'undefined' ) {
						var _use_zoom_control = false;
					}
					
					// init map
					_ui_leaflet_maps[ strMapID ] = L.map( strMapID, {
						zoomControl: _use_zoom_control,
					} );
					
					
					
					var strAttribution = 'Map data &copy; <a href="https://openstreetmap.org">OpenStreetMap</a> contributors, <a href="https://creativecommons.org/licenses/by-sa/2.0/">CC-BY-SA</a>, ';
					
					if( typeof( config.osm_attribution ) != 'undefined' ) {
						strAttribution = config.osm_attribution;
					}
					
					
					// add tile layer (server)
					L.tileLayer( map_layer, {
						maxZoom: 18,
						minZoom: 0,
						zoom: 16,
						subdomains: 'abc',
						attribution: strAttribution,
					}).addTo( _ui_leaflet_maps[strMapID] );
					
					if( _use_zoom_control == false ) {
						//console.log( 'zoom_position:', config.zoom_position );
						
						var _zoom_control_options = {
							position: config.zoom_position,
						};
						/**
						 * Add options for the custom zoom control via global JS object
						 * 
						 * @since 0.9.6
						 */
						
						if( typeof( ui_leaflet_extension_options ) != 'undefined' && typeof( ui_leaflet_extension_options.zoom_control ) != 'undefined' ) {
							var _default_zoom_control_options = _zoom_control_options;
							
							_zoom_control_options = Object.assign( _zoom_control_options, ui_leaflet_extension_options.zoom_control );
							
							/*
							_zoom_control_options = ui_leaflet_extension_options.zoom_control;
							
							if( typeof( _zoom_control_options.position ) == 'undefined' ) {
								_zoom_control_options.position = config.zoom_position;
							}*/
						}
						console.log( 'current zoom control options:', _zoom_control_options );
						
						L.control.zoom( _zoom_control_options ).addTo( _ui_leaflet_maps[ strMapID ] );
						
						/*
						L.control.zoom({
							position: config.zoom_position,
						}).addTo( _ui_leaflet_maps[ strMapID ] );
						*/
					}
					
					// focus
					//var mymap = L.map('mapid').setView([51.505, -0.09], 13);
					//_ui_leaflet_maps[ strMapID ].setView( [51.505, -0.09], 14 );
					
					// add geopos search if enabled
					console.log( 'current config:', config );
					
					if( typeof( config.use_search ) != 'undefined' && typeof( L.Control.geocoder ) != 'undefined' ) { // enabled and geocoder lib loaded
						
						_search_position = 'topright';
						
						if( typeof( config.search_position ) != 'undefined' ) {
							_search_position = config.search_position;
						}
						
						//console.log( 'position:', _search_position );
						
						/**
						 * Custom geocoder options
						 * @since v0.9.6
						 */
						
						_ui_leaflet_geocoder_options = {
							collapsed: false,
							position: _search_position,
						};
						
						if( typeof( ui_leaflet_extension_options.ui_leaflet_geocoder_js ) != 'undefined' ) {
							_ui_leaflet_geocoder_options = Object.assign( _ui_leaflet_geocoder_options, ui_leaflet_extension_options.ui_leaflet_geocoder_js );
						}
						
						
						L.Control.geocoder( _ui_leaflet_geocoder_options ).addTo( _ui_leaflet_maps[ strMapID ] );
						
						/*
						L.Control.geocoder({
							collapsed: false,
							position: _search_position,
						}).addTo( _ui_leaflet_maps[ strMapID ] );
						*/
					}
					
					
					
					_ui_leaflet_maps[ strMapID ].on( 'locationfound', function( e ) {
						//console.info( 'location found:', e );
					});
					
					_ui_leaflet_maps[ strMapID ].setView( {lng: config.longitude, lat: config.latitude}, map_zoomlevel );
					
					/**
					 * Based upon @link https://developer.mapquest.com/documentation/samples/leaflet/v2.2/maps/geolocation/
					 */
					
					if( typeof( config.use_locate ) != 'undefined' && config.use_locate != false ) {
						//console.info( 'using locate' );
						
						//if( typeof( config.locate_popup ) != 'undefined' ) {
						var popup = L.popup();
						
						var strLocDecErrorMsg = '<strong>Error:</strong> The Geolocation service failed.'; // supported, but failed
						var strLocDecUnsupportedMsg = '<strong>Error:</strong> This browser doesn\'t support geolocation.'; // not supported by device
						
						if( typeof( config.msg_locate_error ) != 'undefined' ) {
							strLocDecErrorMsg = config.msg_locate_error;
						}
						
						if( typeof( config.msg_locate_unsupported ) != 'undefined' ) {
							strLocDecUnsupportedMsg = config.msg_locate_unsupported;
						}
					
						
						function geolocationErrorOccurred(geolocationSupported, popup, latLng) {
							popup.setLatLng(latLng);
							popup.setContent(geolocationSupported ?
								strLocDecErrorMsg : 
								strLocDecUnsupportedMsg
							);
							//popup.openOn(geolocationMap);
							
							popup.openOn( _ui_leaflet_maps[ strMapID ] );
						}

						if (navigator.geolocation) {
							
							navigator.geolocation.getCurrentPosition(function(position) {
								var latLng = {
									lat: position.coords.latitude,
									lng: position.coords.longitude
								};
								
								
								// access in global scope on detected position
								_ui_leaflet_maps_position[ strMapID ] = position;
								
								var strPopupContent = '<strong>This</strong> is your current position.';

								if( typeof( config.locate_popup ) != 'undefined' ) {
									strPopupContent = config.locate_popup;
								}

								popup.setLatLng(latLng);
								//popup.setContent( 'This is your current location' );
								popup.setContent( strPopupContent );
								//popup.openOn(geolocationMap);
								
								popup.openOn( _ui_leaflet_maps[ strMapID ] );

								_ui_leaflet_maps[ strMapID ].setView(latLng);
							}, function() {
								//geolocationErrorOccurred(true, popup, geolocationMap.getCenter() );
								geolocationErrorOccurred(true, popup, _ui_leaflet_maps[ strMapID ].getCenter() );
								//geolocationErrorOccurred
								
							});
						} else {
							//No browser support geolocation service
							//geolocationErrorOccurred(false, popup, geolocationMap.getCenter());
							geolocationErrorOccurred(false, popup, _ui_leaflet_maps[ strMapID ].getCenter() );
						}
					
						
						//_ui_leaflet_maps[ strMapID ].locate({ setView: true, timeout: 5000 });
						
						
					}
					
					
					// init marker
					if( jQuery('#' + strMapID + ' script.ui-leaflet-marker' ).length > 0 ) { // found one
						// retrieve it
							
						//jQuery( '<div>' + jQuery('.leaflet-marker').html() + '</div>' ).find('h1,h2,h3,h4,h5,h6').text()
						
						var strMarkerTitle = jQuery( '<div>' + jQuery('script.ui-leaflet-marker').html() + '</div>' ).find('h1,h2,h3,h4,h5,h6').text();
						
						var strMarkerText = jQuery('script.ui-leaflet-marker').html();
						
						
						
						//console.log( 'title:', strMarkerTitle, 'text:', strMarkerText );
						
						
						
						var posMarker = { 'lng': config.longitude, 'lat': config.latitude };
						
						// custom position
						if( typeof( config.marker_position ) != 'undefined' && config.marker_position.latitude != config.latitude && config.marker_position.longitude != config.longitude ) {
							posMarker.lng = config.marker_position.longitude;
							posMarker.lat = config.marker_position.latitude;
						}
						
						
						
						//var strMarkerIcon = '<i class="fa fa-map-pin fa-2x"></i>';
						/**
						 * Change to a more fitting marker icon
						 * @since 0.9.4
						 */
						
						//var strMarkerIcon = '<i class="fa fa-map-marker fa-2x"></i>';
						var strMarkerIcon = '<i class="uil-location uil-2x"></i>';
						
						if( typeof( config.marker_fa_icon ) != 'undefined' && config.marker_fa_icon != '' ) {
							var strMarkerIcon = '<i class="fa ' + config.marker_fa_icon + '"></i>';
						}
						
						/**
						 * Optionally use a FortAwesome 5 icon
						 * @since v0.9.4
						 */
			
						if( typeof( config.marker_far_icon ) != 'undefined' && config.marker_far_icon != ''  ) {
							var strMarkerIcon = '<i class="far fab ' + config.marker_fa_icon + '"></i>';
						}
			
						/**
						 * Use a COMPLETE DIFFERENT icon class (eg. because you generated your own, or want to use glyphicons instead)
						 * @since v0.9.4
						 */
						if( typeof( config.marker_icon_class ) != 'undefined' && config.marker_icon_class != '' ) {
							var strMarkerIcon = '<i class="' + config.marker_icon_class + '"></i>';
						}
			
			
						/**
						 * Use custom HTML for the marker icon instead of the default Font / Fork / Fort Awesome-based icon HTML code
						 * @since v0.9.4
						 */
			
						if( typeof( config.marker_icon_html ) != 'undefined' && config.marker_icon_html != '' ) {
							strMarkerIcon = config.marker_icon_html;
						}
						
			
						// create div-based marker icon
						//L.divIcon({className: 'my-div-icon'});
			
						// add marker to map
						
	
						L.marker( posMarker, {icon: L.divIcon({className: 'ui-leaflet-div-icon', html: strMarkerIcon} ) } ).addTo( _ui_leaflet_maps[strMapID] ).bindPopup( strMarkerText ).openPopup();
						
						jQuery( document ).trigger( '_ui_leaflet_map_marker_added', { 
							'map_id': strMapID,
							'marker': {
								'position': posMarker,
								'icon_class': 'ui-leaflet-div-icon',
								'icon_html': strMarkerIcon,
							},
						} );
						
						// .. and remove marker code from dom, to avoid issues
						jQuery('#' + strMapID + ' script.ui-leaflet-marker' ).remove();
						
					}
				}
				jQuery( document ).trigger( '_ui_leaflet_map_loaded', {
					'map_id': strMapID,
					'settings': {
						'layer': map_layer,
						'zoom_level': map_zoomlevel,
						'longitude': config.longitude, 
						'latitude': config.latitude,
					}
				} ); // base event
				
				
			}
		})
	}
});
