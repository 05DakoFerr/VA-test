<?php

$SERVERLOGBOOK = "serverlogbook.csv";

// Generic pilot flight report receiver

$user = $_GET['user']; //username
$pass =  $_GET['pass']; //user password
$auth =  $_GET['auth']; //server authentication code
$version =  $_GET['version']; //acars software version

$aircraft =  $_GET['aircraft']; //aircraft titleline
$atcmodel  = $_GET['atcModel'];  //aircraft model (typically an ICAO identifier)
$atcdata  =  $_GET['atcData']; //delimited atc data: "/Identifier/Flight Number/Airline/Type/"

//Parse the atcdata string:
$sar = explode("/",$atcdata);
$atcIdentifer    = $sar[0];
$atcFlightNumber = $sar[1];
$atcAirline      = $sar[2];
$atcType         = $sar[3];


$lat1 =  $_GET['lat1']; //origin latitude, decimal degrees
$lon1 =  $_GET['lon1']; //origin longitude, decimal degrees
$lat2 =  $_GET['lat2']; //destination latitude, decimal degrees
$lon2 =  $_GET['lon2']; //destination longitude, decimal degrees
$latland =  $_GET['latland']; //landing latitude, decimal degrees
$lonland =  $_GET['lonland']; //landing longitude, decimal degrees

//Replace commas, if they exist, with dots.
$lat1 = 1 * str_replace(',', '.', $lat1);
$lon1 = 1 * str_replace(',', '.', $lon1);
$lat2 = 1 * str_replace(',', '.', $lat2);
$lon2 = 1 * str_replace(',', '.', $lon2);
$latland = 1 * str_replace(',', '.', $latland);
$lonland = 1 * str_replace(',', '.', $lonland);


$directNM =  $_GET['directNM']; //great circle distance, NM
$actualNM =  $_GET['actualNM']; //flight path distance, NM

$dateshort = $_GET['dateshort']; //Date in the following format: MM/DD/YY
$datestamp = $_GET['datestamp']; //Unix epoch UTC

$timeout = $_GET['timeout']; //HH:MM:SS (UTC)
$timeoff = $_GET['timeoff']; //HH:MM:SS (UTC)
$timeon = $_GET['timeon']; //HH:MM:SS (UTC)
$timein = $_GET['timein']; //HH:MM:SS (UTC)

$fstimeout = $_GET['fstimeout']; //HHMM (UTC)
$fstimeoff = $_GET['fstimeoff']; //HHMM (UTC)
$fstimeon = $_GET['fstimeon']; //HHMM (UTC)
$fstimein = $_GET['fstimein']; //HHMM (UTC)

$blocktime =  $_GET['blocktime']; //block time, decimal hours

$fuelstart =  $_GET['fuelstart']; //starting fuel weight, pounds
$fuelstop =  $_GET['fuelstop']; //ending fuel weight, pounds
$fueldiff =  $_GET['fueldiff']; //fuel used, pounds

$timoday =  $_GET['timoday']; //time of day: Day, Night
$landingFR =  $_GET['landingFR']; // flight rule: VFR, MVFR, IFR, or LIFR
$landingKTS =  $_GET['landingKTS']; //landing speed, knots
$landingFPS =  $_GET['landingFPS']; //landing vertical speed, feet per minute
$takeoffLBS =  $_GET['takeoffLBS']; //takeoff weight, pounds
$landingLBS =  $_GET['landingLBS']; //landing weight, pounds

$pirep =  $_GET['pirep']; //flight comments, string

$pause =  $_GET['pause']; // pause tally, seconds
$slew =  $_GET['slew']; //slew tally, seconds
$stall =  $_GET['stall']; //stall tally, seconds
$overspeed = $_GET['overspeed']; //aircraft overspeed tally, seconds
$speed10K = $_GET['speed10K']; //speed greater than 250 KTS below 10000 FT tally, seconds
$simrate = $_GET['simrate']; //simrate not equal to 1 tally, seconds
$refuel = $_GET['refuel']; //refuel detection, 1=refueled.
$crashed = $_GET['crashed']; //crash detection, 1=crashed.
$nofuel = $_GET['nofuel']; //nofuel detection,1=out of fuel
$warpNM = $_GET['warpNM']; //unusual movement detection, NM

// Use this variable to detect the amount of weight jettisoned:
$ordnance = $_GET['MILdiffLBS']; //takeoff weight - landing weight - fuel used


//Version 1.7
// Landing analysis vis-a-vis selected Landing Signal Officer (LSO) grades.
// Note the grades are based primarily on  AICarrier and TacPack glide slope
// angles (4.12 degrees). Server variable 'lso' contains grade(s) delimited
// with semicolon(s).  Grades are (highest to lowest): _OK_; OK; (OK); -; C.
$lso_data = $_GET['lso'];

$takeoffFR =  $_GET['takeoffFR'];  // flight rule: VFR, MVFR, IFR, or LIFR
$takeoffWind = $_GET['windstart']; // wind direction (DDD) and speed (S) token in metar format (DDDSS)
$landingWind = $_GET['windstop'];  //wind direction (DDD) and speed (S) token in metar format (DDDSS)

$payload = $_GET['payload'];  //difference landing weight and fuel remaining
$fsVersion =  $_GET['fsVersion'];  //flight sim version as reported by fsacars (note that fsuipc sends x-plane version as FSX)
$oew =  $_GET['oew'];  //operating empty weight, pounds
$zfw =  $_GET['zfw'];  //zero fuel weight, pounds
$rollout =  $_GET['rollout'];  //rollout from wheels down to 30 knots ground speed.



//Uncomment the following line to use the economics module
include 'economics_module.php';

//Provided is an airport database and a function that returns the ICAO code based
//on proximity of aircraft to nearest airport
$orig = getICAO($lat1,$lon1);
$dest = getICAO($lat2,$lon2);

// Example of how to log your flight into a simple, delimited database.
// An example of how to enter the data into a mySQL database is available in the server package.
$logmain = sprintf("%s||%s||%s||%s||%s||%s||%s||%s||%s||%s||%s||%s",
	$datestamp,$user,$version,$aircraft,$orig,
	$dest,$blocktime,$fueldiff,$timoday,$landingFR,
	$landingKTS,$landingFPS);

//Append the string to the logbook
$fname = $SERVERLOGBOOK;
$fptr = fopen($fname,"a");
fprintf($fptr,"%s\n",$logmain);
fclose($fptr);



$html=<<<_HTML

<html>
<head>
<title>Your Flight Report </title>
</head>
<body>
<pre>
fsACARS FLIGHT REPORT
==========================================================================

IDENTIFICATION
--------------------------------------------------------------------------
     Username......: $user
     Password......: (suppressed)
     Authentication: $auth
     fsACARS Ver...: $version
     FS Version....: $fsversion


PILOT REPORT (PIREP)
--------------------------------------------------------------------------
     $pirep

FLIGHT
--------------------------------------------------------------------------
     Date..........: $dateshort
     Origin........: $orig
     Destination...: $dest
     Distance......: $directNM
     Equipment.....: $aircraft
     Model.........: $atcmodel
     Aircraft Type.: $atcdata


TIME, DISTANCE AND FUEL
--------------------------------------------------------------------------
     TIME-OUT......: $timeout (FS: $fstimeout)
     TIME-OFF......: $timeoff (FS: $fstimeoff)
     TIME-ON.......: $timeon (FS: $fstimeon)
     TIME-IN.......: $timein (FS: $fstimein)
     BLOCK TIME....: $blocktime
     BLOCK NM......: $actualNM
     FUEL START....: $fuelstart
     FUEL STOP.....: $fuelstop
     FUEL USED.....: $fueldiff


DEPARTURE CONDITIONS
--------------------------------------------------------------------------
     EMPTY WEIGHT..: $oew
     TAKEOFF WEIGHT: $takeoffLBS
     CONDITIONS....: $takeoffFR
     WIND..........: $takeoffWind


APPROACH AND LANDING PERFORMANCE
--------------------------------------------------------------------------
     TIME OF DAY...: $timoday
     CONDITIONS....: $landingFR
     WIND..........: $landingWind
     LANDING KTS...: $landingKTS
     LANDING FPS...: $landingFPS
     LANDING WEIGHT: $landingLBS
     ROLLOUT LENGTH: $rollout


REALISM AUDITING
--------------------------------------------------------------------------
     PAUSE.............: $pause
     SLEW..............: $slew
     STALL.............: $stall
     SIMRATE...........: $simrate
     REFUEL............: $refuel
     CRASHED...........: $crashed
     OUT OF FUEL.......: $nofuel
     UNUSUAL MOVEMENT..: $warpNM
     AIRFRAME OVERSPEED: $overspeed
     AIRSPACE OVERSPEED: $speed10K

OTHER
--------------------------------------------------------------------------
     Payload...............: $payload
     Ordinance LBS.........: $ordnance
     LSO GRADES............: $lso_data
     Oper Empty Weight.....: $oew
     Zero Fuel Weight......: $zfw


PASSENGER-LEVEL ECONOMICS
------------------------------------------------------------------------------

  Total Revenue:.........................................         $revenue_total

  Costs
     Fuel..........................: $cost_fuel
     Flight Crew...................: $cost_crew
     Maintenance (parts)...........: $cost_maint_parts
     Maintenance (labor)...........: $cost_maint_labor
     Landing Fees..................: $cost_landingfees
                                     ------------
     Total Direct Costs..............:             $cost_direct

     Traffic Commissions...........: $cost_traffic
     Communications................: $cost_comm
     Gate Agents...................: $cost_gate
     Marketing.....................: $cost_adpro
     Reservations..................: $cost_reservations
     Administration................: $cost_admin
     Amortization..................: $cost_amort
     Passenger Service (food)......: $cost_paxfood
     Passenger Service (incidental): $cost_paxother
     Transport Related (other).....: $cost_transother
                                     ------------
     Total Indirect Costs............:             $cost_indirect
                                                   ------------

     Total Costs:......................................... :      $cost_total
                                                                  ------------
     Profit:...................................................   $profit
                                                                  ============
------------------------------------------------------------------------------



-------------------------------------------------------------------------
AIR CARGO SIMULATION
-------------------------------------------------------------------------

  Total Revenue .........................................: $cargo_revenue

  Direct Costs
     Fuel..........................: $cargo_fuel
     Materials.....................: $cargo_materials
     Landing Fees..................: $cargo_landing
     Salaries......................: $cargo_salaries
     Benefits......................: $cargo_benefits

  Indirect Costs
     Insurance.....................: $cargo_insurance
     Equipment.....................: $cargo_equipment
     Commissions...................: $cargo_commissions
     Communications................: $cargo_comms
     Advertising...................: $cargo_advertising
     Transport Related Services....: $cargo_trans
     Other Services................: $cargo_oservices

   Total Costs ............................................: $cargo_tcost
                                                            -------------
   Net ...................................................: $cargo_profit
                                                            =============
-------------------------------------------------------------------------




---------------------------------------------------
MILITARY COST
---------------------------------------------------
AVIATION FUEL.................    $mil_fuel
CONSUMABLE SUPPLIES*   .......    $mil_consup
DEPOT LEVEL REPAIRS...........    $mil_dlr
SYSTEM MAINTENANCE............    $mil_sm
                                  ------------
TOTAL FLIGHT OPERATIONS.......    $mil_total

*note: does not include ordinance
---------------------------------------------------



</pre>








</body>
</html>

_HTML
;

$fn = "r_".$user."_".hash("crc32",time()).".html";
$fp = fopen("reports/".$fn,"w");
fprintf($fp,"%s\n",$html);
fclose($fp);


//must return #RXOK# for fsacars to acknowledge the report as being received
//by server and use a single pipe to delimit URL of viewable report
$url = "#RXOK#|".$fn."|";

echo $url;

exit;




//Functions ===========================================================
//=====================================================================

//This function will return the closest ICAO code, and append the distance
// in nautical miles from the closest ICAO code if greater than 10 NM.
//-----------------------------------------------------------------------
function getICAO($lat, $lon)
{
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

