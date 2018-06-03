<?php
function redirect_to($new_location) {
	header("Location: " . $new_location);
	exit; 
}
function mysql_prep($string) {
	global $conn;
	$escaped_string = mysqli_real_escape_string($conn, $string);
	return $escaped_string;
}
function confirm_query($result_set) {
	if (!$result_set) {
		die("Database query failed.");
	}
}
?>