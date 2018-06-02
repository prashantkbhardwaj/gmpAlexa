<?php require_once("../includes/session.php");?>
<?php require_once("../includes/db_connection.php");?>
<?php require_once("../includes/functions.php");?>
<?php confirm_logged_in(); ?>
<?php
	$count_query = "SELECT DISTINCT(drone_id) FROM drones ORDER BY id DESC";
	$count_result = mysqli_query($conn, $count_query);
	while ($count_drone = mysqli_fetch_assoc($count_result)) {
		$drone_id = $count_drone['drone_id'];
		$query = "SELECT * FROM drones WHERE drone_id = '{$drone_id}' ORDER BY id DESC";	
		$result = mysqli_query($conn, $query);
		confirm_query($query);
		$status = mysqli_fetch_assoc($result);
		echo $status['power'].",".$status['batt'].",".$status['flight'].",".$status['drone_id'].",";
	}		 
?>