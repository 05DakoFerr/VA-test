<?php

//Generic example of how to populate an sql database with data from fsACARS
//Note - this has not been thoroughly tested and should be used only as a guide.
//==============================================================================



//Initialize mySQL
//===================================================================
//$hostname = "localhost";
$hostname = "sql.mydomain.de";
$database = "PIREP";  //database name
$username = "ADMIN";   //database username
$password = "aB^7";  //database password
$sqldata = mysql_connect($hostname, $username, $password) or trigger_error(mysql_error(),E_USER_ERROR);
mysql_select_db($database, $sqldata);

//Collect data from fsACARS
//===================================================================
$flightdate = $_GET['dateshort']; // The format is: MM/DD/YY
$user = $_GET['user']; //username
$aircraft =  $_GET['aircraft']; //aircraft titleline
$blocktime =  $_GET['blocktime']; //block time, decimal hours
$pirep =  $_GET['pirep']; //flight comments, string
$lat1 =  $_GET['lat1']; //origin latitude, decimal degrees
$lon1 =  $_GET['lon1']; //origin longitude, decimal degrees
$lat2 =  $_GET['lat2']; //destination latitude, decimal degrees
$lon2 =  $_GET['lon2']; //destination longitude, decimal degrees

//Compute origin and destination airport ICAO codes
//===================================================================
$origin_airport_icao = getICAO($lat1,$lon1);
$destination_airport_icao = getICAO($lat2,$lon2);


//Add flight data to the database
//===================================================================
$sqlcmd = sprintf("INSERT INTO logbook (DATE,USER,ORIG,DEST,EQPT,HOURS,PIREP) VALUES ('%s','%s','%s','%s','%s','%s','%s')", $flightdate, $user, $orig ,$dest, $aircraft, $blocktime, $pirep);
$sqlres = mysql_query($sqlcmd, $atxData) or die(mysql_error());

exit;

//Functions ===========================================================
//=====================================================================

//This function will return the closest ICAO code, and append the distance
// in nautical miles from the closest ICAO code if greater than 10 NM.
//-----------------------------------------------------------------------
function getICAO($lat, $lon)
{

	$lat = 1 * str_replace(',', '.', $lat); //replace comma(s)
	$lon = 1 * str_replace(',', '.', $lon); //replace comma(s)

	$fdat = file("airports.csv");
	$n = count($fdat);

	$mindist = 999;
	$minicao = "0000";

	for($i = 0; $i < $n; $i++){
		$sar = explode(",",$fdat[$i]);

		$d = GetDistance($lat,$lon,$sar[2],$sar[3]);

		if($d < $mindist){
			$mindist = $d;
			$minicao = $sar[0];
		}

	}

	if($mindist > 10) $minicao = sprintf("CVN/%s%d",$minicao,$mindist);

	return $minicao;
}


function GetDistance($lat1,$lon1,$lat2,$lon2)
{
  $lat1 = $lat1 / 180 * pi();
  $lon1 = $lon1 / 180 * pi();
  $lat2 = $lat2 / 180 * pi();
  $lon2 = $lon2 / 180 * pi();
  $dlon = $lon2 - $lon1;
  $dlat = $lat2 - $lat1;
  $a = sin($dlat/2) * sin($dlat/2) + cos($lat1) * cos($lat2) * sin($dlon/2) * sin($dlon/2);
  $b = 2 * atan2(sqrt($a), sqrt(1-$a));
  $nm = 0.54 * 6366.707 * $b;
  return $nm;
}

?>