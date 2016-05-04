<?php
/*
 * CS75 Project 2 - The BART
 * summer 2014
 * gets the next departures from a given station from the BART api
 * 9/21/14 by logan_____
 */ 

require("../data/keys.php"); // get our API keys

// hold the data we get back from BART
class Departures
{
	public $departureTimes;
}

// set MIME type
header("Content-type: application/json");

// read the XML and put the parts we need into a Route object
$dom = simplexml_load_file("http://api.bart.gov/api/etd.aspx?cmd=etd&orig=".$_GET["station"]."&key=".BART_API_KEY);
$_departure_list = $dom->xpath("/root/station/etd/estimate");
$departures = new Departures();
$i = 0;
foreach($_departure_list as $d) {
	$departures->departureTimes[$i] = (string)$d->minutes;
	$i++;
}

// spit out the Departures object in JSON form
echo json_encode($departures);
?>

