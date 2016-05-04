<?php
/*
 * CS75 Project 2 - The BART
 * summer 2014
 * gets information about routes from the BART api
 * 9/20/14 by logan_____
 */ 

require("../data/keys.php"); // get our API keys

// hold the data we get back from BART
class Route
{
	public $stations;
	public $color;
}

// set MIME type
header("Content-type: application/json");

// read the XML and put the parts we need into a Route object
$dom = simplexml_load_file("http://api.bart.gov/api/route.aspx?cmd=routeinfo&route=".$_GET["route"]."&key=".BART_API_KEY);
$__route = $dom->xpath("/root/routes/route")[0];
$stationlist = $__route->config[0]->station;
$route = new Route;
$i = 0;
// build an array of all the stations in the route
foreach($stationlist as $stn) {
	// format each station as { name: $i, value: station_name }
	// the "name", though meaningless, is helpful to have when we get back to javascript
	$route->stations[$i]["value"] = (string)$stn; 
	$route->stations[$i++]["name"] = $i; // the increment seems wrong, but php evaluates the rhs first
}
// get the route color
$route->color = (string)$__route->color;

// spit out the Route object in JSON form
echo json_encode($route);
?>
