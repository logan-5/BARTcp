<?php
/*
 * 	CS75 Project 2: The BART
	summer 2014

	9/13/14 by logan_____
*/

require("../data/keys.php"); // get our API keys

// access the BART api to get a list of all the routes
// the "$routes" array will hold a list of all routes in the following format:
// $routes['abbreviation'] = name, number
// we use this to build our drop-down menu at the bottom of the page
$routes = array();
$dom = simplexml_load_file("http://api.bart.gov/api/route.aspx?cmd=routes&key=".BART_API_KEY);
$_route_list = $dom->xpath("/root/routes/route");
foreach($_route_list as $route) {
		$routes["{$route->abbr}"] = array("name" => $route->name,
											"number" => $route->number);
}

?>
<!DOCTYPE html>
<html>
	<head>
		<style>
			html { height:100% }
			body { height:100% }
			#map-canvas { height: 750; width: 100; margin-left: auto; margin-right: auto }
		</style>
		<script type='text/javascript'
		src=<?='https://maps.googleapis.com/maps/api/js?key='.GOOGLE_API_KEY?>></script>
		<script type='text/javascript' src='http://code.jquery.com/jquery-latest.js'></script>
		<script type="text/javascript" src='map.js'></script>
		<script type="text/javascript">
			function updateMap() {
				showRoute(document.getElementById("routes").value,map);
			}
		</script>
		<title>The BART!</title>
	</head>
	<body onload="updateMap()">
		<div style='text-align:center; font-family:serif'><h1>The BART</h1></div>
		<div id='map-canvas'></div><br/>
		<div style='text-align:center'>Select a route from the list below.<br/>
			<select id="routes" onchange="updateMap()">
			<?php
				foreach($routes as $r) {
					echo "<option value='{$r["number"]}'>{$r["name"]}</option>";
				}
			?>
			</select>
		</div>
	</body>
</html>
