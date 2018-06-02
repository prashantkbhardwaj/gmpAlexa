<?php require_once("../includes/session.php");?>
<?php require_once("../includes/db_connection.php");?>
<?php require_once("../includes/functions.php");?>
<?php confirm_logged_in(); ?>
<?php
	$count_query = "SELECT DISTINCT(drone_id) FROM pings ORDER BY id DESC";
	$count_result = mysqli_query($conn, $count_query);
	while ($count_drone = mysqli_fetch_assoc($count_result)) {
		$drone_id = $count_drone['drone_id'];
		$query = "SELECT * FROM pings WHERE drone_id = '{$drone_id}' ORDER BY id DESC";	
		$result = mysqli_query($conn, $query);
		confirm_query($query);
		$location = mysqli_fetch_assoc($result); 

		date_default_timezone_set('Asia/Calcutta');
    	$current_time = date("Y-m-d\TH:i:s");
    	$loc_time = $location['location_time'];
		$current_time = strtotime($current_time);
		$loc_time = strtotime($loc_time);
		$tdiff = round(abs($current_time - $loc_time) / 60,2);
		if ($tdiff>=5) {
			$power = 0;
		} else {
			$power = 1;
		}

		echo $location['latitude'].",".$location['longitude'].",".$location['altitude'].",".$location['drone_id'].",".$power.",".$location['batt'].",".$location['head'].",".$location['pitch'].",".$location['roll'].",".$location['airspeed'].",".$location['bank'].",".$location['vert_speed'].",".$location['trip_id'].","; 
	}		 
?>