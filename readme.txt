=== UI Leaflet Integration ===
Contributors: usability.idealist
Tags: map, maps, Leaflet, shortcode, OpenStreetMap, OSM, opendata, open data, location, geo, geocoding, geolocation, mapnik, mapquest, mapbox, OpenLayers, mapping, coordinates, geocoding, geotagging, latitude, longitude, position, google maps, googlemaps, gmaps, google map, wms, tms, marker, layer, karte, custom marker text, leaflet map, map shortcode
Requires at least: 4.1
Tested up to: 4.7
Stable tag: trunk
License: GPLv2
License URI: http://www.gnu.org/licenses/gpl-2.0.html

The most less excessive Leaflet map integration. Keeping it simple with a shortcode. Uses the latest Leaflet libary (1.0.1).

== Description ==

Proper integration of the Leaflet map library. Aimed at experienced users and developers.

Currently implemented features:

* Use a simple shortcode with extensive parameter documentation to insert a map anywhere in your site :)
* Create a marker by simply adding content inside the map shortcode
* Optionally position the marker somewhere else on that map
* A few helpful filter hooks
* On-demand loading of the respective JS files

= Future plans = 

* Multiple markers (shortcode)
* Quick address lookup 
* Shortcode insertion via a nice user interface in the editor
* A settings page (there are already options in place, just no interface to change em)

= Website =

http://f2w.de/ui-leaflet-integration


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


= Available tile service handles =

* default - uses the OSM mapnik tile server 
* osm_bw - OSM mapnik in black and white
* mapquest


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

= Q. I have a question =
A. Chances are, someone else has asked it. Either check out the support forum at WP or take a look at the official issue tracker:
http://github.com/ginsterbusch/ui-leaflet-integration/issues


== Screenshots ==

1. Insert shortcode in the editor

2. End result

== Changelog ==

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
