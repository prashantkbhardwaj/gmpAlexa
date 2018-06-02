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
    echo "ui";
?> 
<?php
    if (isset($_POST['submit'])) {
        $drones = implode("_", $_POST['choice']);
        redirect_to("multiple_view.php?drones=$drones");
    }
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
                    <a href="logout.php"><i class="fa fa-sign-out"></i></a>                   
                </li>
                <li class="dropdown">
                    <a href="#"><i class="fa fa-user"></i> <?php echo htmlentities($name_title["username"]); ?> </b></a>                    
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
                    <li>
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
                    <li class="active">
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
                        <ol class="breadcrumb">
                            <i class="fa fa-fw fa-map-marker"></i>
                            <li>
                                Please select the drones you want to see together.
                            </li>                            
                        </ol>
                    </div>
                </div>
                <textarea id="dcount" style="display: none;"><?php echo $total_drones; ?></textarea>
                <textarea  id="responsecontainer" style="display: none;"></textarea>
                <!-- /.row -->
                <br>
                <div class="row">
                    <div class="col-lg-12">
                        <form method="post" action="multiple.php">
                            <div class="table-responisve">
                                <table class="table table-bordered table-hover table-striped">
                                    <thead>
                                        <tr>
                                            <th>Select</th>
                                            <th>Drone ID</th>
                                            <th>Latitude</th>
                                            <th>Longitude</th>
                                            <th>Heading</th>      
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php
                                            while ($count_drone = mysqli_fetch_assoc($count_result)) { ?>
                                                <tr>
                                                    <td><input type="checkbox" name="choice[]" value="<?php echo $count_drone['drone_id']; ?>" class="form-group"></td>
                                                    <td><?php echo $count_drone['drone_id']; ?></td>   
                                                    <td><p id="<?php echo "lat_id".$count_drone['drone_id']; ?>"></p></td>
                                                    <td><p id="<?php echo "lon_id".$count_drone['drone_id']; ?>"></p></td>
                                                    <td><p id="<?php echo "head_id".$count_drone['drone_id']; ?>"></p></td>
                                                </tr>    
                                                <?php   
                                            }
                                        ?>
                                    </tbody>                                                     
                                </table>                           
                            </div>    
                            <input type="submit" name="submit" class="btn btn-lg btn-success" value="Submit"> 
                        </form>
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
    <!-- <script src="https://maps.googleapis.com/maps/api/js?v=3.exp&sensor=false"></script> -->
    <script type="text/javascript">         
        $(document).ready(function() {
            $("#responsecontainer").load("select.php");
            
            var refreshId = setInterval(function() {
                $("#responsecontainer").load('select.php?randval='+ Math.random());
                var x = document.getElementById("responsecontainer").value;         
                var dcount = document.getElementById("dcount").value;
                var loop = dcount*13;               
                var locate = x.split(',');              
                var d_id = new Array(loop);
                var head_id = new Array(loop);
                var lat_id = new Array(loop);
                var lon_id = new Array(loop);
                var b = 12;
                for (var i = 12; i <loop; i+=13) {
                    
                    d_id[i-b] = locate[i-9];   
                    head_id[i-b] = locate[i-6];    
                    lat_id[i-b] = locate[i-12];
                    lon_id[i-b] = locate[i-11]; 
                    
                    document.getElementById("head_id"+d_id[i-b]).innerHTML = head_id[i-b]+" deg. ";
                    document.getElementById("lat_id"+d_id[i-b]).innerHTML = lat_id[i-b];
                    document.getElementById("lon_id"+d_id[i-b]).innerHTML = lon_id[i-b]; 
                    
                    b = 12*i; 
                };                                  
            }, 1000);
            $.ajaxSetup({ cache: false});       
        });     
    </script> 
</body>

</html>
<?php
if (isset ($conn)){
    mysqli_close($conn);
}
?>
