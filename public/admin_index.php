<?php require_once("../includes/session.php");?>
<?php require_once("../includes/db_connection.php");?>
<?php require_once("../includes/functions.php");?>
<?php confirm_admin_logged_in(); ?>
<?php
	$current_user = $_SESSION["username"];
	$name_query = "SELECT * FROM admins WHERE username = '{$current_user}' LIMIT 1";
	$name_result = mysqli_query($conn, $name_query);
	confirm_query($name_result);
	$name_title = mysqli_fetch_assoc($name_result);
?>
<?php
	$query = "SELECT * FROM hubs";
	$result =  mysqli_query($conn, $query);
	confirm_query($result);
?>
<?php include("../includes/layouts/header.php");?>

<div id="main">
	<div id="navigation">
		<ul>
			<li><a href="admin_logout.php">Logout</a></li><br/><br/>
			<li><a href="signup.php">Create a new Hub account</a></li><br/><br/>
		</ul>
	</div>	
	<div id="page">
		<h3>Hub List</h3>
		<p>
			<?php
				while ($hub_list = mysqli_fetch_assoc($result)) {
					echo $hub_list['username']."<br>";
				}
			?>
		</p>		
	</div>
</div>
<?php include("../includes/layouts/footer.php");?>