<?php

// fsACARS.com
// Generic flight tracking script.  fsACARS periodically sends information
// about the flight to the server.  This script will parse that information
// so can track your pilots in real time.
//
//

//probably want more validation than this...

	$user      =  $_GET['user'];      //username
	$pass      =  $_GET['pass'];      //user password
	$auth      =  $_GET['auth'];      //auth code
	$lat1      =  $_GET['lat1'];      //starting latitude
	$lon1      =  $_GET['lon1'];      //starting longitude
	$lat2      =  $_GET['lat2'];      //current latitude
	$lon2      =  $_GET['lon2'];      //current longitude
	$msl       =  $_GET['msl'];       //current altitude , MSL, feet
	$gskts     =  $_GET['gskts'];     //current ground speed, knots
	$hdgtrue   =  $_GET['hdgtrue'];   //true heading, degrees
	$etime     =  $_GET['etime'];     //elapsed time, decimal hours
	$titleline =  $_GET['titleline']; //aircraft model from title in aircraft.cfg
	$onground  =  $_GET['onground'];  //1=on ground


	//Replaces commas with periods for numerics
	$lat1 = 1 * str_replace(',', '.', $lat1);
	$lon1 = 1 * str_replace(',', '.', $lon1);
	$lat2 = 1 * str_replace(',', '.', $lat2);
	$lon2 = 1 * str_replace(',', '.', $lon2);


if($user != ""){

	$fp = fopen("posreps.csv","a");
	fprintf($fp,"%d||%s||%s||%s||%d||%d\n",time(),$user,$lat2,$lon2,$msl,$gskts);
	fclose($fp);

	exit;
}


// If this script is called from outside fsACARS, it will display a simple flight tracking table.


printf("<html><head><title>fsACARS Position Reporting System</title><meta http-equiv='refresh' content='180'></head><body>\n");
printf("<center>");
printf("<h4>fsACARS Generic Position Reporting</h4>\n");

$tmnow = time();

$fdata = file("posreps.csv");
$n = count($fdata);
for($i = 0; $i < $n; $i++){
	$sar = explode("||",$fdata[$i]);
	$tdiff = $tmnow - $sar[0];
	if($tdiff < 1200){ //20 minutes
		$flightarr[$sar[1]]["LAT"] = $sar[2];
		$flightarr[$sar[1]]["LON"] = $sar[3];
		$flightarr[$sar[1]]["MSL"] = $sar[4];
		$flightarr[$sar[1]]["KTS"] = $sar[5];
	}
}

if(count($flightarr) <= 0){
	printf("<hr />\n");
	printf("Currently there are no flights underway.\n");
	exit;
}


printf("<table border=1 >\n");

printf("<tr><th>USER</th> <th>LAT</th> <th>LON</th> <th>MSL</th><th>GSKTS</th></tr>\n");
$googlemaps = array();
$ark = array_keys($flightarr);
$n = count($ark);


for($i = 0; $i < $n; $i++){
	$k = $ark[$i];
	printf("<tr><td align=center>%s</td> <td align=center>%s</td> <td align=center>%s</td> <td align=center>%s</td> <td align=center>%s</td> </tr>\n",$k,$flightarr[$k]["LAT"],$flightarr[$k]["LON"],$flightarr[$k]["MSL"], $flightarr[$k]["KTS"]);

	$sumlat += $flightarr[$k]["LAT"]*1;
	$avglat = $sumlat / ($i+1);
	$sumlon += $flightarr[$k]["LON"]*1;
	$avglon = $sumlon / ($i+1);

	$markers .= sprintf("&markers=color:red|label:X|%.4f,%.4f",$flightarr[$k]["LAT"],$flightarr[$k]["LON"]);
}
$googlemaps= sprintf("http://maps.googleapis.com/maps/api/staticmap?center=%.1f,%.1f&zoom=2&sensor=false&size=800x400&maptype=roadmap",$avglat,$avglon);

$googlemaps .= $markers;

//printf("<tr> <td colspan=5><img alt='' src='$googlemaps'></td></tr>\n");


printf("</table>");



// By request here is a function to convert lat and lon from
// decimal degree (dd) format to a specific degree decimal minute (ddm) format
//
// usage example:
//
// $str = latlon_dd2ddm(37.3906,-118.007233);
// echo $str; // prints the string, 'N37 23.4360 W118 0.4340'
//
function latlon_dd2ddm($lat, $lon){

	$lat_dir = "N";
	if($lat < 0) $lat_dir = "S";
	$lat = abs($lat);
	$lat_deg = 1 * sprintf("%d",$lat);
	$lat_min = 60 * ($lat - $lat_deg);


	$lon_dir = "E";
	if($lon < 0) $lon_dir = "W";
	$lon = abs($lon);
	$lon_deg = 1 * sprintf("%d",$lon);
	$lon_min = 60 * ($lon - $lon_deg);


	$latlon = sprintf("%s%d %.4f %s%d %.4f",$lat_dir, $lat_deg, $lat_min, $lon_dir, $lon_deg, $lon_min);
	return $latlon;
}


?>
