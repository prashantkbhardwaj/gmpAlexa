<?php require_once("../includes/session.php");?>
<?php require_once("../includes/db_connection.php");?>
<?php require_once("../includes/functions.php");?>
<?php confirm_logged_in(); ?>
<?php 
    $current_user = $_SESSION["username"];
    $name_query = "SELECT * FROM hubs WHERE username = '{$current_user}' LIMIT 1";
    $name_result = mysqli_query($conn, $name_query);
    confirm_query($name_result);
    $name_title = mysqli_fetch_assoc($name_result);
?>
<?php
    $count = "SELECT count( DISTINCT(drone_id) ) FROM pings";
    $count_result = mysqli_query($conn, $count);
    confirm_query($count_result);
    $row = mysqli_fetch_array($count_result);
    $total_drones = $row[0];    
?>
<?php
    $count_query = "SELECT DISTINCT(drone_id) FROM pings WHERE hub_id = '{$current_user}' ORDER BY id DESC";
    $count_result = mysqli_query($conn, $count_query);
    $list_result = mysqli_query($conn, $count_query);
    $start_result = mysqli_query($conn, $count_query);
    $trip_result = mysqli_query($conn, $count_query);
    $consumer_result = mysqli_query($conn, $count_query);
?> 
<?php
    $trip_id = $_GET['trip_num'];
    $did = $_GET['drone_id'];
    $detail_query = "SELECT * FROM trips WHERE trip_id = '{$trip_id}' AND did = '{$did}' LIMIT 1";
    $detail_result = mysqli_query($conn, $detail_query);
    confirm_query($detail_result);
    $detail_list = mysqli_fetch_assoc($detail_result);
?>
<?php
    $pos_query = "SELECT * FROM pings WHERE trip_id = '{$trip_id}' AND drone_id = '{$did}' AND hub_id = '{$current_user}'";
    $pos_result = mysqli_query($conn, $pos_query);
    confirm_query($pos_result);
    $lat_result = mysqli_query($conn, $pos_query);
    confirm_query($lat_result);
    $lon_result = mysqli_query($conn, $pos_query);
    confirm_query($lon_result);
    $time_result = mysqli_query($conn, $pos_query);
    confirm_query($time_result);
?>
<!DOCTYPE html>
<html lang="en"> 
 
<head>

    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="Drone Swarm and Navigation System for drones by R2 Robotronics">
    <meta name="author" content="Prashant Bhardwaj">

    <title>R2 Robotronics | Drone Swarm and Navigation System</title>

    <!-- Bootstrap Core CSS -->
    <link href="css/bootstrap.min.css" rel="stylesheet">

    <!-- Custom CSS -->
    <link href="css/sb-admin.css" rel="stylesheet">

    <!-- Custom Fonts -->
    <link href="font-awesome/css/font-awesome.min.css" rel="stylesheet" type="text/css">
    <link rel="stylesheet" type="text/css" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.6.3/css/font-awesome.min.css">
    <script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyDGjc9sby68TeD--EaZMovfRE8bRFXtxz8&v=3.exp"></script>
    <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
    <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
    <!--[if lt IE 9]>
        <script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
        <script src="https://oss.maxcdn.com/libs/respond.js/1.4.2/respond.min.js"></script>
    <![endif]-->

</head>

<body>

    <div id="wrapper">

        <!-- Navigation -->
        <nav class="navbar navbar-inverse navbar-fixed-top" role="navigation">
            <!-- Brand and toggle get grouped for better mobile display -->
            <div class="navbar-header">
                <button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".navbar-ex1-collapse">
                    <span class="sr-only">Toggle navigation</span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                </button>
                <a class="navbar-brand" href="index.php">R2 Robotronics</a>
            </div>
            <!-- Top Menu Items -->
            <ul class="nav navbar-right top-nav">  
                <li class="dropdown">
                    <a href="#">
                        <span>
                            <img style="height:100%; width:50%;" src="images/esri.png">
                        </span>
                    </a>
                </li>                
                <li class="dropdown">
                    <a href="logout.php"><i class="fa fa-sign-out"></i></a>                    
                </li>
                <li class="dropdown">
                    <a href="#"><i class="fa fa-user"></i> <?php echo htmlentities($name_title["username"]); ?> </a>                    
                </li>
            </ul>
            <!-- Sidebar Menu Items - These collapse to the responsive navigation menu on small screens -->
            <div class="collapse navbar-collapse navbar-ex1-collapse">
                <ul class="nav navbar-nav side-nav">
                    <li>
                        <a href="index.php"><i class="fa fa-fw fa-dashboard"></i> Dashboard</a>
                    </li>
                    <li>
                        <a href="javascript:;" data-toggle="collapse" data-target="#track"><i class="fa fa-fw fa-location-arrow"></i> Track <i class="fa fa-fw fa-caret-down"></i></a>
                        <ul id="track" class="collapse">
                           <?php
                                while ($list_drone = mysqli_fetch_assoc($list_result)) { ?>
                                    <li>
                                        <a href="map.php?drone_id=<?php echo urlencode($list_drone['drone_id']); ?>">
                                            Drone <?php echo $list_drone['drone_id']; ?>       
                                        </a>
                                    </li>
                                    <?php
                                }
                            ?>
                        </ul>
                    </li>
                    <li>
                        <a href="javascript:;" data-toggle="collapse" data-target="#start"><i class="fa fa-fw fa-fighter-jet"></i> Start a trip <i class="fa fa-fw fa-caret-down"></i></a>
                        <ul id="start" class="collapse">
                            <?php
                                while ($start_drone = mysqli_fetch_assoc($start_result)) { ?>
                                    <li>
                                        <a href="start_trip.php?drone_id=<?php echo urlencode($start_drone['drone_id']); ?>">
                                            Drone <?php echo $start_drone['drone_id']; ?>       
                                        </a>
                                    </li>
                                    <?php
                                }
                            ?>
                        </ul>
                    </li>
                    <li class="active">
                        <a href="javascript:;" data-toggle="collapse" data-target="#trip"><i class="fa fa-fw fa-archive"></i> Trip record <i class="fa fa-fw fa-caret-down"></i></a>
                        <ul id="trip" class="collapse">
                            <?php
                                while ($trip_drone = mysqli_fetch_assoc($trip_result)) { ?>
                                    <li>
                                        <a href="trip.php?drone_id=<?php echo urlencode($trip_drone['drone_id']); ?>">
                                            Drone <?php echo $trip_drone['drone_id']; ?>       
                                        </a>
                                    </li>
                                    <?php
                                }
                            ?>
                        </ul>
                    </li>
                    <li>
                        <a href="javascript:;" data-toggle="collapse" data-target="#consumer"><i class="fa fa-fw fa-user-secret"></i> Consumer details <i class="fa fa-fw fa-caret-down"></i></a>
                        <ul id="consumer" class="collapse">
                            <?php
                                while ($consumer_drone = mysqli_fetch_assoc($consumer_result)) { ?>
                                    <li>
                                        <a href="consumer.php?drone_id=<?php echo urlencode($consumer_drone['drone_id']); ?>">
                                            Drone <?php echo $consumer_drone['drone_id']; ?>       
                                        </a>
                                    </li>
                                    <?php
                                }
                            ?>
                        </ul>
                    </li>    
                    <li>
                        <a href="multiple.php"><i class="fa fa-fw fa-crosshairs"></i> Multiple access</a>
                    </li>     
                </ul>
            </div>
            <!-- /.navbar-collapse -->
        </nav>

        <div id="page-wrapper">

            <div class="container-fluid">

                <!-- Page Heading -->
                <div class="row">
                    <div class="col-lg-12">
                        <h1 class="page-header"><i class="fa fa-archive"></i>
                            Trip details <small>Trip #<?php echo $trip_id; ?> </small>
                        </h1>
                        <ol class="breadcrumb">
                            <li>
                                <i class="fa fa-user"></i> <?php echo $detail_list['cust_name']; ?>
                            </li>       
                            <li>
                                <i class="fa fa-briefcase"></i> <?php echo $detail_list['product']; ?>
                            </li> 
                            <li>
                                <i class="fa fa-balance-scale"></i> <?php echo $detail_list['payload']; ?> gms
                            </li>                                  
                        </ol>
                    </div>
                </div>
                <!-- /.row -->

                <div class="row">                    
                    <div class="col-lg-12">
                        <center>
                            <h3>Click on the markers to know their location time.</h3>
                            <div style="width:900px;height:480px;" id="googleMap"></div>
                        </center>
                    </div>
                </div><br><br>
                <!-- /.row -->   
                <div class="row">
                    <div class="col-lg-12 text-center">
                        <div class="table-responsive">
                            <table class="table table-bordered table-hover table-striped">
                                <thead>
                                    <tr>
                                        <th><center>Time</center></th>
                                        <th><center>Latitude</center></th>
                                        <th><center>Longitude</center></th>
                                    </tr>
                                </thead>            
                                <tbody>
                                    <?php
                                        while ($pos_list = mysqli_fetch_assoc($pos_result)) { ?>
                                            <tr>
                                                <td><?php echo $pos_list['location_time']; ?></td>
                                                <td><?php echo $pos_list['latitude']; ?></td>
                                                <td><?php echo $pos_list['longitude']; ?></td>
                                            </tr> 
                                            <?php
                                        }
                                    ?>                                   
                                </tbody>                    
                            </table>
                        </div><br><br>
                        <textarea style="display:none;" id="mark_lat">
                            <?php
                                while ($mark_lat = mysqli_fetch_assoc($lat_result)) {
                                    echo $mark_lat['latitude']."_";
                                }
                            ?>
                        </textarea>
                        <textarea style="display:none;" id="mark_lon">
                            <?php
                                while ($mark_lon = mysqli_fetch_assoc($lon_result)) {
                                    echo $mark_lon['longitude']."_";
                                }
                            ?>
                        </textarea>
                        <textarea style="display:none;" id="mark_time">
                            <?php
                                while ($mark_time = mysqli_fetch_assoc($time_result)) {
                                    echo $mark_time['location_time']."_";
                                }
                            ?>
                        </textarea>
                    </div>
                </div>            

            </div>
            <!-- /.container-fluid -->

        </div>
        <!-- /#page-wrapper -->

    </div>
    <!-- /#wrapper -->

    <!-- jQuery -->
    <script src="js/jquery.js"></script>

    <!-- Bootstrap Core JavaScript -->
    <script src="js/bootstrap.min.js"></script>
    <script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyC8hbCo346Mcq6rHyTE3Niwn5gVhaWwKcs"></script>
    <script>    
        function initialize()
        {   
            var x = document.getElementById("mark_lat").value;
            var y = document.getElementById("mark_lon").value;
            var t = document.getElementById("mark_time").value;
            var locate_x = x.split("_");
            var locate_y = y.split("_");
            var locate_t = t.split("_");
            var loop = locate_x.length - 1;
            var marker;
            var marker_info;
            var position = new Array(loop);
            var mapProp = { 
                center: new google.maps.LatLng(locate_x[0], locate_y[0]),        
                zoom:18,
                mapTypeId: 'satellite'
            };  
            var map = new google.maps.Map(document.getElementById("googleMap"),mapProp);
            var labels = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
            
            for (var i = 0; i < loop; i++) {
                position[i]=new google.maps.LatLng(locate_x[i], locate_y[i]);

                marker = new google.maps.Marker({
                    position: position[i],
                    map: map,     
                    label: labels[i],       
                });

                marker_info = new google.maps.InfoWindow()
                var content = locate_t[i];
                google.maps.event.addListener(marker,'click', (function(marker,content,marker_info){ 
                    return function() {
                       marker_info.setContent(content);
                       marker_info.open(map,marker);
                    };
                })(marker,content,marker_info));
            }
            var line = new google.maps.Polyline({
                path: position,
                strokeColor: "#030EFC",
                strokeOpacity: 1.0,
                strokeWeight: 2,
                map: map
            });  
        }
        google.maps.event.addDomListener(window, 'load', initialize);       
    </script> 
</body>

</html>
<?php
if (isset ($conn)){
    mysqli_close($conn);
}
?>