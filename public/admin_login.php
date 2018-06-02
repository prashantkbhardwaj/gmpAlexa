<?php require_once("../includes/session.php");?>
<?php require_once("../includes/db_connection.php");?>
<?php require_once("../includes/functions.php");?>
<?php require_once("../includes/validation_functions.php"); ?>
<?php
if (admin_logged_in()) {
	redirect_to ("admin_index.php");
}
?>
<?php
$username = "";
if (isset($_POST['submit'])) {

	$required_fields = array("username", "password");
	validate_presence($required_fields);
	
	if (empty($errors)) {

		$username = $_POST['username'];		
		$password = $_POST['password'];
		$found_admin = attempt_admin_login($username, $password);

		if ($found_admin) {

			$_SESSION["admin_id"] = $found_admin["id"];
			$_SESSION["username"] = $found_admin["username"];
			redirect_to("admin_index.php");
		} else {
			$_SESSION["message"] = "Username/password not found.";
		}
	}
} else {

}
?>
<?php include("../includes/layouts/header.php");?>
<div id="main">
<div id="navigation">
	&nbsp;
</div>
<div id="page">
<?php echo message(); ?>
<?php echo form_errors($errors); ?>

<h2>Login</h2>
	<p>Enter the <b>admin</b> username and password</p>
<p><form action="admin_login.php" method="post">
<table>
	<tr>
		<td>Username</td>
		<td><input type="text" name="username" value="" required/></td>
	</tr>
	<tr>
		<td>Password </td>
		<td><input type="password" name="password" value="" required></td>
	</tr>
	<tr>
		<td><input name="submit" type="submit" value="Log in"></td>
	</tr>
</table>
</form>
</p>
<p>
	<a href="../index.php">Log in as Hub</a>
</p>
</div>
</div>
<?php include("../includes/layouts/footer.php");?>