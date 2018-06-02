<?php require_once("../includes/db_connection.php");?>

<?php
	if (isset($_GET['drone_id'])) { $drone_id = mysqli_real_escape_string($conn, htmlspecialchars($_GET['drone_id']));	} else { $drone_id = "0"; }
	if (isset($_GET['altitude'])) { $altitude = mysqli_real_escape_string($conn, htmlspecialchars($_GET['altitude']));	} else { $altitude = "0"; }
	if (isset($_GET['latitude'])) { $latitude = mysqli_real_escape_string($conn, htmlspecialchars($_GET['latitude']));	} else { $latitude = "0"; }
	if (isset($_GET['longitude'])) { $longitude = mysqli_real_escape_string($conn, htmlspecialchars($_GET['longitude'])); } else { $longitude = "0"; }	
	if (isset($_GET['hub_id'])) { $hub_id = mysqli_real_escape_string($conn, htmlspecialchars($_GET['hub_id'])); } else { $hub_id = "0"; }		
	if (isset($_GET['batt'])) { $batt = mysqli_real_escape_string($conn, htmlspecialchars($_GET['batt'])); } else { $batt = "0"; }	
	if (isset($_GET['head'])) { $head = mysqli_real_escape_string($conn, htmlspecialchars($_GET['head'])); } else { $head = "0"; }
	if (isset($_GET['pitch'])) { $pitch = mysqli_real_escape_string($conn, htmlspecialchars($_GET['pitch'])); } else { $pitch = "0"; }
	if (isset($_GET['roll'])) { $roll = mysqli_real_escape_string($conn, htmlspecialchars($_GET['roll'])); } else { $roll = "0"; }
	if (isset($_GET['airspeed'])) { $airspeed = mysqli_real_escape_string($conn, htmlspecialchars($_GET['airspeed'])); } else { $airspeed = "0"; }
	if (isset($_GET['bank'])) { $bank = mysqli_real_escape_string($conn, htmlspecialchars($_GET['bank'])); } else { $bank = "0"; }
	if (isset($_GET['vert_speed'])) { $vert_speed = mysqli_real_escape_string($conn, htmlspecialchars($_GET['vert_speed'])); } else { $vert_speed = "0"; }
	if (isset($_GET['trip_id'])) { $trip_id = mysqli_real_escape_string($conn, htmlspecialchars($_GET['trip_id'])); } else { $trip_id = "0"; }
	
	date_default_timezone_set('Asia/Calcutta');
    $location_time = date("Y-m-d\TH:i:s");

    //echo "ping recevd";

   	$query = "INSERT INTO pings (drone_id, altitude, latitude, longitude, location_time, hub_id, batt, head, pitch, roll, airspeed, bank, vert_speed, trip_id) VALUES ('{$drone_id}', '{$altitude}', '{$latitude}', '{$longitude}', '{$location_time}', '{$hub_id}', '{$batt}', '{$head}', '{$pitch}', '{$roll}', '{$airspeed}', '{$bank}', '{$vert_speed}', '{$trip_id}')";
   	mysqli_query($conn, $query);
?>  