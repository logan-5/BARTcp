<? // gets the next departures from a given station from the BART api
	// 9/21/14 by logan_____
	
	require("../data/keys.php"); // get our API keys
	
	// hold the data we get back from BART
	class Departures
	{
		public $departures;
	}
	
	// set MIME type
//	header("Content-type: application/json");
	
	// read the XML and put the parts we need into a Route object
	$dom = simplexml_load_file("http://api.bart.gov/api/route.aspx?cmd=routeinfo&route=".$_GET["route"]."&key=".BART_API_KEY);
	$__route = $dom->xpath("/root/routes/route")[0];
	$stationlist = $__route->config[0]->station;
	$route = new Route;
	$i = 0;
	foreach($stationlist as $stn) {
		$route->stations[$i] = (string)$stn; $i++;
	}
	$route->color = (string)$__route->color;
	
	// spit out the Route object in JSON form
//	echo json_encode($route);

var_dump($schedule);
?>

