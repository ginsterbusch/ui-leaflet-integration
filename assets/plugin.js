/**
 * Leaflet handler (theoretically requires no jQuery, but we use it anyway, just because ;) :P )
 * 
 * @version 0.5
 * Changelog:
 * v0.6:
 * - integration of geocoder / geosearch
 * 
 * v0.5:
 * - enhanced custom events with useful information
 * - event data always include the map ID
 * v0.4:
 * - added custom events
 */

jQuery( function() {
	// setup variables
	_ui_leaflet_maps = {}; // global object for further access
		
	
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
					
					// init map
					_ui_leaflet_maps[ strMapID ] = L.map( strMapID );
					
					
					// add tile layer (server)
					L.tileLayer( map_layer, {
						maxZoom: 18,
						minZoom: 0,
						zoom: 16,
						subdomains: 'abc',
						attribution: 'Map data &copy; <a href="http://openstreetmap.org">OpenStreetMap</a> contributors, ' +
							'<a href="http://creativecommons.org/licenses/by-sa/2.0/">CC-BY-SA</a>, ',
					}).addTo( _ui_leaflet_maps[strMapID] );
					
					// focus
					//var mymap = L.map('mapid').setView([51.505, -0.09], 13);
					//_ui_leaflet_maps[ strMapID ].setView( [51.505, -0.09], 14 );
					
					// add geopos search if enabled
					console.log( 'current config:', config );
					
					if( typeof( config.use_search ) != 'undefined' && typeof( L.Control.geocoder ) != 'undefined' ) { // enabled and geocoder lib loaded
						
						_search_position = 'topleft';
						
						if( typeof( config.search_position ) != 'undefined' ) {
							_search_position = _search_position;
						}
						
						L.Control.geocoder({
							collapsed: false,
							position: _search_position,
						}).addTo( _ui_leaflet_maps[ strMapID ] );
					}
					
					_ui_leaflet_maps[ strMapID ].setView( {lng: config.longitude, lat: config.latitude}, map_zoomlevel );
					
					
					//_ui_leaflet_maps[ strMapID ].setView( {lng: config.longitude, lat: config.latitude}, 14 );
					
					
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
						
						var strMarkerIcon = '<i class="fa fa-map-pin fa-2x"></i>';
						if( typeof( config.marker_fa_icon ) != 'undefined' ) {
							var strMarkerIcon = '<i class="fa ' + config.marker_fa_icon + '"></i>';
						}
			
						// create div-based marker icon
						//L.divIcon({className: 'my-div-icon'});
			
						// add marker to map
						
	
						L.marker( posMarker, {icon: L.divIcon({className: 'ui-leaflet-div-icon', html: strMarkerIcon} ) } ).addTo( _ui_leaflet_maps[strMapID] ).bindPopup( strMarkerText ).openPopup();
						
						jQuery( document ).trigger( '_ui_leaflet_map_marker_added', { 
							'map_id': strMapID,
							'marker': {
								'position': posMarker,
								'icon_class': 'ui-leaflet-div-icon'
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
