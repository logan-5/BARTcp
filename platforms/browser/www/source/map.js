/*
 * CS75 Project 2 - The BART
 * summer 2014
 * map.js
 * Handles user input and ajax requests for a google maps object
 * 9/20 - 9/21/2014 by logan_____
 */
/////////////////////////////////////////////////////
// global variables
var currentLine; // holds a reference to the polyline currently drawn, if any
var currentMarkers; // holds an array of the currently drawn markers representing stations
var infowindow = new google.maps.InfoWindow(); // the single info window we'll manipulate
var map; // our map object we'll be rendering to

/////////////////////////////////////////////////////
// initialize the map.  called upon page load
function setUpMap() {
	// we could center directly over san francisco
	// but I thought it was more aesthetically pleasing over the 12th St station
	// but we'll save them both just in case
	var SF_LatLng = { lat: 37.7577, lng: -122.4376 };
	var O12TH_LatLng = { lat: 37.803664, lng: -122.271604 };// O as in Oakland (can't start a variable name with a number)

	var mapOptions = {
			center: O12TH_LatLng,
			zoom: 10
		};
	map = new google.maps.Map(document.getElementById('map-canvas'), mapOptions);
}
google.maps.event.addDomListener(window, 'load', initialize);

/////////////////////////////////////////////////////
// ajax, user-input, and helper functions

// takes an array of stations and returns an array of google.maps.LatLng objects
function stationsToLatLng(stationlist) {
	var LatLngs = new Array(stationlist.length);
	for (i = 0; i < stationlist.length; i++)
		LatLngs[i] = new google.maps.LatLng(stationlist[i].lat, stationlist[i].lng);
	return LatLngs;
}

// gets data about the given station using ajax and displays the info window
function triggerInfoWindow(marker, _map) {
	$.ajax({ url: "departures.php",
			data: { station: marker.getTitle() },
			success: function(data) {
				var output = "Next departures from this station:<br>";
				for(i = 0; i < data.departureTimes.length; i++) {
					output += data.departureTimes[i]=="Leaving" ?
								"Now<br>" :
								data.departureTimes[i] + " minute" +
									(data.departureTimes[i]==1 ? "":"s")+" from now<br>";
								// bro that was a sick nested ternary operator
				}
				infowindow.setContent(output);
				infowindow.open(map, marker);
			}
		});
}

// remove and delete all markers from a list
function clearMarkers(markerlist) {
	if(!markerlist) return false;
	var tempmarker;
	for(i=0; i < markerlist.length; i++) {
		tempmarker = markerlist[i];
		tempmarker.setMap(null);
		tempmarker = null;
	}
	markerlist = null;
}

// returns a function to be used as an event
// necessary because "i" must be evaluated now,
// before it goes out of scope
function getMarkerFunction(i, _map) {
	var marker = currentMarkers[i];
	return function() { triggerInfoWindow(marker, map) };
}

// draw each station in a list as a marker on the map
// also storing them in currentMarkers
function drawStations(stationlist, _map) {
	clearMarkers(currentMarkers);
	currentMarkers = new Array(stationlist.length);
	var markerPos = stationsToLatLng(stationlist);
	for(i = 0; i < stationlist.length; i++) {
		currentMarkers[i] = new google.maps.Marker({
								position: markerPos[i],
								title: stationlist[i].abbr,
								//animation: google.maps.Animation.DROP
							});
			// the following block drops the markers in a nice way
			// unfortunately with as many markers as we're dealing with,
			// it looks real crappy
			// READ: also must uncomment "animation" line above^
			/*	setTimeout(function(i) {
							currentMarkers[i].setMap(map);
							}, i * 200, i);	*/

		// so we'll just do this instead:
			currentMarkers[i].setMap(map);

			// now listen for clicks and trigger the info window if there is one
			google.maps.event.addListener(currentMarkers[i], 'click',
											getMarkerFunction(i, map));
	}
}

// remove and delete a polyline
function clearLine(polyline) {
	if(!polyline) return false;
	polyline.setMap(null);
	polyline = null;
}

// draw polylines of a route given list of stations and color
function drawRoute(stationlist, color, _map) {
	var routePath = stationsToLatLng(stationlist); // get the path array from the station list
	var route = new google.maps.Polyline({
		path: routePath,
		geodesic: true,
		strokeColor: color,
		strokeOpacity: 0.9,
		strokeWeight: 5 });

	// erase the current line and draw the new one
	clearLine(currentLine);
	currentLine = route;
	currentLine.setMap(map);
}

// requests data for a given route number from BART api and local data
// then displays it on the given map as polylines and station markers
// station markers get their station information via ajax when clicked
function showRoute(num, _map) {
	$.ajax({url: "route.php",
			data: { route: num },
			success: function(data) {
				// create an array to be used by
				var stations = new Array(data.stations.length);
				var color = data.color;
				for(i = 0; i < data.stations.length; i++)
					stations[i] = { name: i, value: data.stations[i]};
				$.ajax({url: "stations.php",
						//data: $.param(stations, true),
						data: $.param(data.stations, true),
						success: function(data) {
							drawRoute(data, color, map);
							drawStations(data, map);
							}
						});
				}
	});
}
