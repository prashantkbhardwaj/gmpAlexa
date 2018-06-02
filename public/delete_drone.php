<?php require_once("../includes/session.php");?>
<?php require_once("../includes/db_connection.php");?>
<?php require_once("../includes/functions.php");?>
<?php confirm_logged_in(); ?>
<?php 
$drone = find_drone_by_id($_GET["drone_id"]);
if (!$drone) {
	redirect_to("landing.php");
}

$drone_id = $drone["drone_id"];
$query = "DELETE FROM drones WHERE drone_id = {$drone_id}";
$result = mysqli_query($conn, $query);

if ($result && mysqli_affected_rows($conn) >= 1) {

	$_SESSION["message"] = "Drone deleted.";
	redirect_to("landing.php");
} else {

	$_SESSION["message"] = "Drone deletion failed.";
	redirect_to("landing.php");
}

?>
