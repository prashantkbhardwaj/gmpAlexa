<?php require_once("../includes/db_connection.php");?>
<?php require_once("../includes/functions.php");?>
<?php
	$count_query = "SELECT DISTINCT(drone_id) FROM pings ORDER BY id DESC";
	$count_result = mysqli_query($conn, $count_query);
	while ($count_drone = mysqli_fetch_assoc($count_result)) { ?>
		<center>
			<a href="map.php?drone_id=<?php echo urlencode($count_drone['drone_id']); ?>"><?php echo "Track Drone ".$count_drone['drone_id']; ?></a><br>
		</center> <?php
	}		 
?>
