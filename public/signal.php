<?php require_once("../includes/db_pie.php");?>
<?php require_once("../includes/functions.php");?>

<?php
    $signal = $_GET['signal'];
    
        
    $query = "INSERT INTO flight_signal (signal) VALUES ("1")";
    mysqli_query($conn_pi, $query); 

    
    redirect_to("http://52.77.245.85/cloud_nav_sys_v2/public/map.php?drone_id=35");
    
?>

<?php
if (isset ($conn_pi)){
    mysqli_close($conn_pi);
}
?>
