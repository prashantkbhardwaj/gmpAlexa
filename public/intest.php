<?php require_once("../includes/db_pie.php");?>
<?php require_once("../includes/functions.php");?>

<?php
    $did = $_GET['drone_id'];
    $trip_id = $_GET['trip_id'];
    $opt_s = $_GET['opt_s'];
    $camera_s = $_GET['camera_s'];
    $way_lat = $_GET['way_lat'];
    $way_lon = $_GET['way_lon'];
    $altitude = $_GET['altitude'];
    $hub = $_GET['hub'];    
    $camera_type = $_GET['camera_type'];
        
    $query = "INSERT INTO trips (did, trip_id, hub, altitude, way_lat, way_lon) VALUES ({$did}, {$trip_id}, '{$hub}', '{$altitude}', '{$way_lat}', '{$way_lon}')";
    mysqli_query($conn_pi, $query); 

    $count_query = "SELECT * FROM trips WHERE trip_id = {$trip_id} LIMIT 1";
    $count_result = mysqli_query($conn_pi, $count_query);
    confirm_query($count_result);
    $count_title = mysqli_fetch_assoc($count_result);
    $lats = $count_title['way_lat'];  
    $lons = $count_title['way_lon'];  
    $altitude = $count_title['altitude'];
    $latis = explode('_' , $lats);      
    $longs = explode('_', $lons);   
    $opt = explode("_", $opt_s);
    $camera = explode("_", $camera_s);
    $num_waypoints = count($latis);
    for ($i=0; $i < $num_waypoints-1; $i++) { 
        $way_lati[$i] = $latis[$i+1];
        $way_lati[$i] = number_format((float)$way_lati[$i], 7, '', '');
        $way_long[$i] = $longs[$i+1];
        $way_long[$i] = number_format((float)$way_long[$i], 7, '', '');
        //$dest[$i] = $e_dest[0].", ".$e_dest[1];            

        $query = "INSERT INTO ways (did, trip_id, hub, way_lati, way_long, altitude, opt, camera, camera_type) VALUES ({$did}, {$trip_id}, '{$current_user}', '{$way_lati[$i]}', '{$way_long[$i]}', '{$altitude}', {$opt[$i]}, {$camera[$i], {$camera_type}})";
        mysqli_query($conn_pi, $query);

        
    }   
    redirect_to("http://52.77.245.85/cloud_nav_sys_v2/public/pending.php?did=$did&trip_id=$trip_id");
    
?>

<?php
if (isset ($conn_pi)){
    mysqli_close($conn_pi);
}
?>
