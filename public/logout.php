<?php require_once("../includes/session.php");?>
<?php require_once("../includes/functions.php");?>
<?php
session_start();
$_SESSION = array();
if (isset($_COOKIE[session_name()])) {
	setcookie(session_name(), '', time()-42000, '/');
}
session_destroy();
redirect_to("../index.php");
?>