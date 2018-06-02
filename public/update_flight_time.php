<?php require_once("../includes/session.php");?>
<?php require_once("../includes/db_connection.php");?> 
<?php require_once("../includes/functions.php");?>
<?php confirm_logged_in(); ?>
<?php 
	$flight_time = $_POST['flight_time1'];
	$trip_id = $_POST['trip_id1'];
	$did = $_POST['did1'];

    $timeQuery = "UPDATE trips SET flight_time = '{$flight_time}', atc_status = 2 WHERE trip_id = '{$trip_id}' AND did = '{$did}' LIMIT 1";
    $timeResult = mysqli_query($conn, $timeQuery);
    confirm_query($timeResult);
    if ($timeResult) {
    	echo "Time added";
    } else {
    	echo "fail";
    }
?>
<?php
if (isset ($conn)){
    mysqli_close($conn);
}
?>