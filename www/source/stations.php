<?php
/*
 * CS75 Project 2 - The BART
 * summer 2014
 * gets information about stations in a route from the BART api
 * 9/20/14 by logan_____
 */ 
	
require("../data/keys.php"); // get our API keys

class Station
{
	public $lat;
	public $lng;
	public $abbr;
}

// set MIME type
header("Content-type: application/json");

//  the following line gets the data from the bart API	
//	$dom = simplexml_load_file("http://api.bart.gov/api/stn.aspx?cmd=stns&key=".BART_API_KEY);
//  we'll cache it locally and do this instead
$dom = simplexml_load_file("../data/stations.xml");

$stations = array();
$i = 0;
foreach($_GET as $stn) {
	$s = $dom->xpath("/root/stations/station[abbr='{$stn}']")[0];
	$stations[$i] = new Station;
	$stations[$i]->lat = (string)$s->gtfs_latitude;
	$stations[$i]->lng = (string)$s->gtfs_longitude;
	$stations[$i]->abbr = (string)$s->abbr;
	$i++;
}
echo json_encode($stations);
?>
