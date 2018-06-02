<?php require_once("../includes/session.php");?>
<?php require_once("../includes/db_connection.php");?>
<?php require_once("../includes/functions.php");?>
<?php confirm_logged_in(); ?>
<?php
	$did = $_GET['did'];
	$trip_id = $_GET['trip_id'];
	$query = "SELECT atc_status FROM trips WHERE did = '{$did}' AND trip_id = '{$trip_id}' LIMIT 1";
	$result = mysqli_query($conn, $query);
	confirm_query($conn, $query);
	$list = mysqli_fetch_assoc($result);

	if ($list['atc_status']==1) {
		echo "<script>$('#start').modal('show');</script>";   
	}		 
?>