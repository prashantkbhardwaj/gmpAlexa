<?php require_once("includes/db_connection.php");?>
<?php
    $lat = "0";
    $lon = "0";
    $placeName = "0";
    $source = "0";
    $dest = "0";
    if (isset($_GET['lat'])) { $lat = $_GET['lat']; } else { $lat = "0"; }
    if (isset($_GET['lon'])) { $lon = $_GET['lon']; } else { $lon = "0"; }
    if (isset($_GET['placeName'])) { $placeName = $_GET['placeName'];   } else { $placeName = "0"; }
    if (isset($_GET['source'])) { $source = $_GET['source'];   } else { $source = "0"; }
    if (isset($_GET['dest'])) { $dest = $_GET['dest'];   } else { $dest = "0"; }

    $query = "INSERT INTO pings (lat, lon, placeName) VALUES ('{$lat}', '{$lon}', '{$placeName}')";
   	mysqli_query($conn, $query);
?>