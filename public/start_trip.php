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
    $did = $_GET['drone_id'];
    $trip_id = rand();
    if (isset($_POST['submit'])) {      
        $cust_name = $_POST['cust_name'];
        $product = $_POST['product'];
        $payload = $_POST['payload'];
        $altitude = $_POST['altitude'];
        $hub = $current_user;
        $camera_type = $_POST['camera_type'];
        $battVolt = $_POST['battVolt'];
        $battErr = $_POST['battErr'];
        //$dest = mysqli_real_escape_string($conn, htmlspecialchars($_POST['dest']));
        
        $query = "INSERT INTO trips (cust_name, product, payload, did, trip_id, hub, altitude, camera_type, battVolt, battErr) VALUES ('{$cust_name}', '{$product}', '{$payload}', {$did}, {$trip_id}, '{$hub}', '{$altitude}', {$camera_type}, '{$battVolt}', '{$battErr}')";
        mysqli_query($conn, $query);
        
        if ($did==2) {
            $mview = "way_esri";
        } else {
            if ($_POST['roam_type']=="waypoints") {
                $mview = "waypoints";
            } else {
                $mview = "polyway";
            }
        }
                                        
        redirect_to($mview.".php?trip_id=$trip_id&drone_id=$did");
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
                    <li class="active">
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
                        <h1 class="page-header"><i class="fa fa-fw fa-rocket"></i>
                            Take off initialization
                        </h1>
                        <ol class="breadcrumb">
                            <li>
                                <i class="fa fa-plane"></i> Details for Drone <?php echo $did; ?>
                            </li>                            
                        </ol>
                    </div>
                </div>
                <!-- /.row -->

                <div class="row">
                    <div class="col-lg-6">

                        <form role="form" method="post" action="start_trip.php?drone_id=<?php echo $did; ?>">

                            <div class="form-group">
                                <label>Trip ID <?php echo $trip_id; ?></label>                                
                            </div>

                            <div class="form-group">                                
                                <input type="text" class="form-control" name="cust_name" placeholder="Customer's Name" required>
                            </div>

                            <div class="form-group">                                
                                <input type="text" class="form-control" name="product" placeholder="Product" required>
                            </div>

                            <div class="form-group">                                
                                <input type="text" class="form-control" name="payload" placeholder="Payload in grams" required>
                            </div>

                            <div class="form-group">                                
                                <input type="number" min="30" max="130" class="form-control" name="altitude" placeholder="Altitude" required>
                            </div>

                            <div class="form-group">                                
                                <select name="camera_type" class="form-control">
                                    <option value="0">Select Camera</option>
                                    <option value="1">GoPro</option>
                                    <option value="2">Xiaomi Yi</option>
                                    <option value="3">Parrot Sequoia</option>
                                </select>
                            </div>

                            <div class="form-group">                                
                                <input type="text" class="form-control" name="battVolt" placeholder="Battery Voltage" required>
                            </div>
                            <div class="form-group">                                
                                <input type="text" class="form-control" name="battErr" placeholder="Battery Error" required>
                            </div>

                            <div class="form-group">                                
                                <select name="roam_type" class="form-control">
                                    <option selected disabled>Select navigation type</option>
                                    <option value="waypoints">Point to point</option>
                                    <option value="polyway">Site Scaning</option>
                                </select>
                            </div>

                            
                            <input type="submit" name="submit" value="Submit" class="btn btn-success">

                        </form><br><br><br>

                    </div>
                    
                </div>
                <!-- /.row -->

            </div>
            <!-- /.container-fluid -->

        </div>
        <!-- /#page-wrapper -->

    </div>
    <!-- /#wrapper -->

    <!-- jQuery -->
    <script src="js/jquery.js"></script>
    <script src="http://code.jquery.com/jquery-latest.js"></script> 
    <!-- Bootstrap Core JavaScript -->
    <script src="js/bootstrap.min.js"></script>

</body>

</html>
<?php
if (isset ($conn)){
    mysqli_close($conn);
}
?>
