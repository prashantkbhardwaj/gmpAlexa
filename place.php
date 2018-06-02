<?php require_once("includes/db_connection.php");?>
<?php
    $lat = "0";
    $lon = "0";
    $placeName = "0";
    if (isset($_GET['lat'])) { $lat = $_GET['lat']; } else { $lat = "0"; }
    if (isset($_GET['lon'])) { $lon = $_GET['lon']; } else { $lon = "0"; }
    if (isset($_GET['placeName'])) { $placeName = $_GET['placeName'];   } else { $placeName = "0"; }
    if (isset($_GET['address'])) { $address = $_GET['address']; } else { $address = "0"; }
    if (isset($_GET['eta'])) { $eta = $_GET['eta']; } else { $eta = "0"; }

    $query = "INSERT INTO pings (lat, lon, placeName, address, eta) VALUES ('{$lat}', '{$lon}', '{$placeName}', '{$address}', '{$eta}')";
   	mysqli_query($conn, $query);
?>