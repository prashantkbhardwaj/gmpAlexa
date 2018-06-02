<?php require_once("includes/db_connection.php");?>
<?php
	$query = "SELECT * FROM pings ORDER BY id DESC LIMIT 1";
	$result = mysqli_query($conn, $query);
	$list = mysqli_fetch_assoc($result);
	$check = "0";
	if ($list['lat'] != "") {
		$check = "1";
	}

	echo $list['lat'].",".$list['lon'].",".$list['placeName'].",".$check.",".$list['address'].",".$list['eta'];
?>