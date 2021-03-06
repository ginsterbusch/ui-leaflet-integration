=== UI Leaflet Integration ===
Contributors: usability.idealist
Tags: map, maps, Leaflet, shortcode, OpenStreetMap, OSM, opendata, open data, location, geo, geocoding, geolocation, mapnik, mapquest, mapbox, OpenLayers, mapping, coordinates, geocoding, geotagging, latitude, longitude, position, google maps, googlemaps, gmaps, google map, wms, tms, marker, layer, karte, custom marker text, leaflet map, map shortcode
Requires at least: 4.1
Tested up to: 5.3
Stable tag: trunk
License: GPLv2
License URI: http://www.gnu.org/licenses/gpl-2.0.html

The most less excessive Leaflet map integration. Keeping it simple with a shortcode. Uses the latest Leaflet libary (1.6).

== Description ==

Proper integration of the Leaflet map library. Aimed at experienced users and developers.

Currently implemented features:

* Use a simple shortcode with extensive parameter documentation to insert a map anywhere in your site :)
* Create a marker by simply adding content inside the map shortcode
* Optionally position the marker somewhere else on that map
* A few helpful filter hooks
* On-demand loading of the respective JS files (more or less)
* Always the latest version of Leaflet.js, with switching option to older versions (filter hook `ui_leaflet_load_leaflet_version`; available versions: 0.7.7, 1.0.1, 1.2, 1.3, 1.6) 
* Optional simple search field overlay using Leaflet Control Geocoder by Per Liedman (https://github.com/perliedman/leaflet-control-geocoder)
* Optional geolocation request (browser / mobile)

= Work in progress =

* Quick address lookup with history (last 50 entries)
* Admin screen for settings + before-mentioned address lookup
* Drop-in default config file (for the WP uploads directory, normally located in `wp-content/uploads`
* Filter hook documentation

= Future plans = 

* Multiple markers (shortcode)
* Shortcode insertion via a nice user interface in the editor
* Properly implemented shortcode asset loading via a separate class / plugin

= Website =

https://github.com/ginsterbusch/ui-leaflet-integration


= Please Vote and Enjoy =
Your votes really make a difference! Thanks.


== Installation ==

1. Upload 'ui-leaflet-integration' to the '/wp-content/plugins/' directory
2. Activate the plugin through the 'Plugins' menu in WordPress
3. Edit an existing post or create a new one
4. Insert the map shortcode '[ui_leaflet_map]' including the latitude and longitude (eg. `[ui_leaflet_map latitude="" longitude=""]`)
5. Read the documentation for better customization :)

== Frequently Asked Questions ==

= Shortcode documentation =

Regular shortcode: `[ui_leaflet_map latitude="" longitude=""]`
Shortcode with marker text: `[ui_leaflet_map latitude="" longitude=""]<strong>A fancy title!</strong>Your marker text here.[/ui_leaflet_map]`

All available shortcode attributes:

* latitude
* longitude
* position - a shorthand for the longitude and latitude parameter. basically the classic "latitude,longitude" position data. For quick copy + paste actions :)
* zoom - initial zoom level of the map. Most map tile services only support a maximum zoom level of 18 (eg. OSM). Defaults to 16.
* marker - if you want to set a marker, but at a different position. Format is identical to the **position** attribute
* layer - either a handle or a specific tile server URL. For the former, see "available handles" below - for the latter, see the Leaflet documentation.
* class - Defaults to 'ui-leaflet-marker'. Only change if you know what you're doing.
* marker_class - Defaults to 'ui-leaflet-marker'. Same here: Only change if you definitely KNOW what you're doing ;)
* id - The ID template for the leaflet map. Defaults to 'ui-leaflet-map-id-%s'. %s is being replaced by the current map count.
* marker_id - Marker ID template. Defaults to 'ui-leaflet-map-id-%s'. %s is replaced with a combination of the map count and marker count.
* height - Height of the map. Defaults to '300px'. Please do NOT forget about adding a unit. Just a plain number, eg. 300, DOES NOT work.
* use_search - set to 'true', 'yes' or '1' to enable the optional search field control in the frontend
* search_position - where to position the search field control; available options are: 'topleft', 'topright', 'bottomleft', 'bottomright'
* use_locate - Locate user; defaults to false. Set to 'true', '1' or 'yes' to enable it.
* locate_marker - Optional location marker for the locate option. set to 'true', '1' or 'yes' to enable it, or enter some fancy text to be displayed in place of the defaul 'This is your current position' text.
* marker_icon_class - defaults to 'fa fa-fw fa-marker-map fa-2x' (= FontAwesome 4.x / Fork Awesome 1.x).
* marker_icon_fa_class - Font/ForkAwesome; if no 'fa-' is given, the plugin automagically adds it
* marker_icon_far_class - Fort Awesome 5.x; dito
* marker_icon_html - Custom html source coeefor the icon, eg. an <img src=".." /> or <svg> ..


= Available tile service handles =

* default - uses the OSM mapnik tile server 
* osm_bw - OSM mapnik in black and white
* mapquest
* work in progress: proper filter hook + JSON file to read in the tile service handling / tile providers automagically

= Q. Where to place the marker text? =
A. Right inside the shortcode, eg. `[ui_leaflet_map]your <strong>nifty</strong> HTML text :)[/ui_leaflet_map]

= Q. What is with this latitude and longitude thingy? =

A. If you came this far and really still have to ask, then you are wrong here. Please [read it all up in wikipedia](https://en.wikipedia.org/wiki/Geographic_coordinate_system) and then come back! :P

= Q. How to get the correct geographic / position data? =

A. A few years ago, it was quite easy to just extract it from the Google Maps HTML code ("integrate it on your homepage" or so).
Sadly, that has changed. But wait, there are better methods nowadays :)

The currently easiest method is:

* Look up the address in the [OSM Nominatim service](http://nominatim.openstreetmap.org)
* When the result is displayed, click on "details" of the correct "result box" (or open it in a new tab)
* In the "details" view, search for "Centre Point" - this contains the latitude and longitude (format: "latitude, longitude")
* Done :)

**Example:**

* Open OSM Nominatim
* Search for "Fischbrunnen, München" (it's a famous fountain in Munich, right on the Marienplatz :))
* [Click on "details"](http://nominatim.openstreetmap.org/details.php?place_id=86088942)
* "Centre Point" should be "48.1372317,11.5761799974694"
* The **latitude** is `48.1372317`, the **longitude** is `11.5761799974694`
* Thus the shortcode would be: `[ui_leaflet_map latitude="48.1372317" longitude="11.5761799974694"]`


= Q. Default position =
A. If no position is set, the map latitude and longitude defaults to "48.1372568, 11.5759285", which is located on the Marienplatz in Munich.
Actually, it's nearly identical to the position shown in the example :D

= Q. Loading a different Leaflet.js version =

There are several (programmatical) options available. First would be to load just a different local version:

* Use the `ui_leaflet_load_leaflet_version` filter hook to switch between the different versions supplied in the `assets` directory - just add the version as a **string**, eg. '0.7.7' for the old stable release.
* Available versions: 0.7.7, 1.0.1, 1.2, 1.3, 1.6

Second choice: Loading external libraries, as suggested by the Leaflet.js download page.

* Use the filter hook `ui_leaflet_js_url' for the JavaScript file ..
* .. and the filter hook `ui_leaflet_css_url` for the CSS file 

** Example**

```<?php
function my_custom_leaflet_js() {
return 'https://unpkg.com/leaflet@1.3.1/dist/leaflet.js';
}

add_filter( 'ui_leaflet_js_url', 'my_custom_leaflet_js' );

function my_custom_leaflet_css() {
return 'https://unpkg.com/leaflet@1.3.1/dist/leaflet.css';
}

add_filter( 'ui_leaflet_css_url', 'my_custom_leaflet_css' );
?>``` 


= Q. I have a question =
A. Chances are, someone else has asked it. Either check out the support forum at WP or take a look at the official issue tracker:
https://github.com/ginsterbusch/ui-leaflet-integration/issues


== Screenshots ==

1. Insert shortcode in the editor

2. End result

== Changelog ==

= 0.9.6 =

* Tweak: Added custom extension options via filter hook (`ui_leaflet_add_extension_options`), exposed in JS as global object `ui_leaflet_extension_options`

= 0.9.5 =

* Tweak: Added marker extraction routine for the main shortcode (in preparation for future versions)
* Tweak: Use filter hook to enforce assets loading (`_ui_leaflet_load_assets`)
* Added experimental custom icons (SVG / CSS)
* Several bugfixes


= 0.9.4 =
* SSL-related bugfixes
* Improved icon classes (use Fork/FontAwesome, Fort Awesome icons, your custom icon library OR replace the complete icon HTML code with your own)
* Default marker is now the map-marker instead of the -pin
* of corpse I forgot to update this readme-file again (not to mention the wiki) xD

= 0.9.3 =

* Bugfixes for the custom extension loader
* Optionally retrieve the user location using the Location API (including fancy marker / popup window and customizable text)

= 0.9 =

* Add custom extensions using filter hooks (`ui_leaflet_add_extensions_js` and `ui_leaflet_add_extensions_js`)

= 0.8.1 =

* Bugfix: Search control position 

= 0.8 =

* Added optional simple search field overlay using Leaflet Control Geocoder by Per Liedman (https://github.com/perliedman/leaflet-control-geocoder)
* Updated Leaflet.js to the latest stable version (1.6)

= 0.6 =

* Updated leaflet.js to the latest stable version (1.3.3)

= 0.5.1 =

* Forgot to update this very bloody Readme file ... *ehem*

= 0.5 =

* Updated leaflet.js to the latest stable version (1.3)
* Minor changes or updates in the plugin code

= 0.4 =

* Updated leaflet.js to the latest stable version (1.2)
* Removed the on-demand asset loading (which is going to resurface in a separate library in the near future)
* Added option for linking to several pre-defined route services (Google Maps, OSRM and GraphHopper so far)

= 0.3 =

* Updated Leaflet.js to the latest stable version (1.0.1).

= 0.2 =

* Initial public release
* Fixed some nasty race conditions with the on-demand loading

= 0.1 =

* UI Leaflet Integration
* .. after testing numerous different map plugins, which either had clumsy interfaces, crappy documentation or always tried to pester you into going "premium", I decided to write my own, simple one :)


== Upgrade Notice ==

None yet.
