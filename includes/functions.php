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
function form_errors ($errors=array()) {
	$output = "";
	if (!empty($errors)) {
		$output .= "<div class=\"error\">";
		$output .= "Please fix the following errors:";
		$output .= "<ul>";
		foreach ($errors as $key => $error) {
			$output .= "<li>";
			$output .= htmlentities($error);
			$output .= "</li>";
		}
		$output .= "</ul>";
		$output .= "</div>";
	}
	return $output;
}
function find_all_users() {
	global $conn;

	$query = "SELECT * ";
	$query .= "FROM hubs ";
	$query .= "ORDER BY username ASC";
	$user_set = mysqli_query($conn, $query);
	confirm_query($user_set);
	return $user_set;
}
function find_user_by_id($user_id) {
	global $conn;

	$safe_user_id = mysqli_real_escape_string($conn, $user_id);

	$query = "SELECT * ";
	$query .= "FROM hubs ";
	$query .= "WHERE id = {$safe_user_id} ";
	$query .= "LIMIT 1";
	$user_set = mysqli_query($conn, $query);
	confirm_query($user_set);
	if ($user = mysqli_fetch_assoc($user_set)) {
		return $user;
	} else {
		return null;
	}
}
function find_all_admins() {
	global $conn;

	$query = "SELECT * ";
	$query .= "FROM admins ";
	$query .= "ORDER BY username ASC";
	$admin_set = mysqli_query($conn, $query);
	confirm_query($admin_set);
	return $admin_set;
}

function find_all_drones() {
	global $conn;

	$query = "SELECT * ";
	$query .= "FROM drones ";
	$query .= "ORDER BY username ASC";
	$drone_set = mysqli_query($conn, $query);
	confirm_query($drone_set);
	return $drone_set;
}

function find_admin_by_id($admin_id) {
	global $conn;

	$safe_admin_id = mysqli_real_escape_string($conn, $admin_id);

	$query = "SELECT * ";
	$query .= "FROM admins ";
	$query .= "WHERE id = {$safe_admin_id}";
	$query .= "LIMIT 1";
	$admin_set = mysqli_query($conn, $query);
	confirm_query($admin_set);
	if ($admin = mysqli_fetch_assoc($admin_set)) {
		return $admin;
	} else {
		return null;
	}
}

function find_drone_by_id($drone_id) {
	global $conn;

	$safe_drone_id = mysqli_real_escape_string($conn, $drone_id);

	$query = "SELECT * ";
	$query .= "FROM drones ";
	$query .= "WHERE id = {$safe_drone_id}";
	$query .= "LIMIT 1";
	$drone_set = mysqli_query($conn, $query);
	confirm_query($drone_set);
	if ($drone = mysqli_fetch_assoc($drone_set)) {
		return $drone;
	} else {
		return null;
	}
}

function password_encrypt($password) {
	$hash_format = "$2y$10$";
	$salt_length = 22;
	$salt = generate_salt($salt_length);
	$format_and_salt = $hash_format . $salt;
	$hash = crypt($password, $format_and_salt);
	return $hash; 
}
function generate_salt($length) {
	$unique_random_string = md5(uniqid(mt_rand(), true));
	$base64_string = base64_encode($unique_random_string);
	$modified_base64_string = str_replace('+', '.', $base64_string);
	$salt = substr($modified_base64_string, 0, $length);
	return $salt;
}
function password_check($password, $existing_hash) {
$hash = crypt($password, $existing_hash);
if ($hash === $existing_hash) {
	return true;
} else {
	return false;
}
}
function find_user_by_username($username) {
	global $conn;
	$safe_username = mysqli_real_escape_string($conn, $username);

	$query = "SELECT * ";
	$query .= "FROM hubs ";
	$query .= "WHERE username = '{$safe_username}' ";
	$query .= "LIMIT 1";
	$user_set = mysqli_query($conn, $query);
	confirm_query($user_set);
	if ($user = mysqli_fetch_assoc($user_set)) {
		return $user;
	} else {
		return null;
	}
}
function find_admin_by_username($username) {
	global $conn;
	$safe_username = mysqli_real_escape_string($conn, $username);

	$query = "SELECT * ";
	$query .= "FROM admins ";
	$query .= "WHERE username = '{$safe_username}' ";
	$query .= "LIMIT 1";
	$admin_set = mysqli_query($conn, $query);
	confirm_query($admin_set);
	if ($admin = mysqli_fetch_assoc($admin_set)) {
		return $admin;
	} else {
		return null;
	}
}

function find_drone_by_username($username) {
	global $conn;
	$safe_username = mysqli_real_escape_string($conn, $username);

	$query = "SELECT * ";
	$query .= "FROM drones ";
	$query .= "WHERE username = '{$safe_username}' ";
	$query .= "LIMIT 1";
	$drone_set = mysqli_query($conn, $query);
	confirm_query($drone_set);
	if ($drone = mysqli_fetch_assoc($drone_set)) {
		return $drone;
	} else {
		return null;
	}
}

function attempt_login($username, $password) {
	$user = find_user_by_username($username);
	if ($user) {
		if (password_check($password, $user["hashed_password"])) {
			return $user;
		} else {
			return false;
		}
	} else {
		return false;
	}
}
function attempt_admin_login($username, $password) {
	$admin = find_admin_by_username($username);
	if ($admin) {
		if (password_check($password, $admin["hashed_password"])) {
			return $admin;
		} else {
			return false;
		}
	} else {
		return false;
	}
}

function attempt_drone_login($username, $password) {
	$drone = find_drone_by_username($username);
	if ($drone) {
		if (password_check($password, $drone["hashed_password"])) {
			return $drone;
		} else {
			return false;
		}
	} else {
		return false;
	}
}

function logged_in() {
	return isset($_SESSION['user_id']);
}
function admin_logged_in() {
	return isset($_SESSION['admin_id']);
}

function drone_logged_in() {
	return isset($_SESSION['drone_id']);
}

function confirm_logged_in() {
	if (!logged_in()) {
		redirect_to("../index.php");
	}
}
function confirm_admin_logged_in() {
	if (!admin_logged_in()) {
		redirect_to("index.php");
	}
}

function confirm_drone_logged_in() {
	if (!drone_logged_in()) {
		redirect_to("drone_login.php");
	}
}

?>