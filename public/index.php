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
    $last_update = "SELECT UPDATE_TIME FROM information_schema.tables WHERE  TABLE_SCHEMA = 'r2robotronics' AND TABLE_NAME = 'pings'";
    $last_result = mysqli_query($conn, $last_update);
    $ans = mysqli_fetch_array($last_result);
    $last_ans = $ans[0];
    echo $last_ans;
?>

<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="Cloud navigation system for drones by R2 Robotronics">
    <meta name="author" content="Prashant Bhardwaj">

    <title>R2 Robotronics | Drone Swarm and Navigation System</title>
   


    <!-- Bootstrap Core CSS -->
    <link href="css/bootstrap.min.css" rel="stylesheet">

    <!-- Custom CSS -->
    <link href="css/sb-admin.css" rel="stylesheet">

    <!-- Morris Charts CSS -->
    <!-- <link href="css/plugins/morris.css" rel="stylesheet"> -->

    <!-- Custom Fonts -->
    <link href="font-awesome/css/font-awesome.min.css" rel="stylesheet" type="text/css">
    <link rel="stylesheet" type="text/css" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.6.3/css/font-awesome.min.css">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.1.0/jquery.min.js"></script>
    
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
                <a class="navbar-brand" href="index.php">                    
                    R2 Robotronics
                </a>
            </div>
            <!-- Top Menu Items -->
            <ul class="nav navbar-right top-nav">
                <li class="dropdown">
                    <a href="#">
                        <span>
                      
                        </span>
                    </a>
                </li>                
                <li class="dropdown">
                    <a href="logout.php"><i class="fa fa-sign-out"></i> </a>
                </li>
                <li class="dropdown">
                    <a href="#"><i class="fa fa-user"></i> <?php echo htmlentities($name_title["username"]); ?> </a>                   
                </li>                
            </ul>
            
            <textarea id="dcount" style="display: none;" ><?php echo $total_drones; ?></textarea>
            <textarea  id="responsecontainer" style="display: none;" ></textarea>
            <!-- Sidebar Menu Items - These collapse to the responsive navigation menu on small screens -->
            <div class="collapse navbar-collapse navbar-ex1-collapse">
                <ul class="nav navbar-nav side-nav">
                    <li class="active">
                        <a href="index.php"><i class="fa fa-fw fa-desktop"></i> Dashboard</a>
                    </li>
                    <li>
                        <a href="javascript:;" data-toggle="collapse" data-target="#track"><i class="fa fa-fw fa-location-arrow"></i> Track <i class="fa fa-fw fa-caret-down"></i></a>
                        <ul id="track" class="collapse">
                           <?php
                                while ($list_drone = mysqli_fetch_assoc($list_result)) { ?>
                                    <?php
                                            if ($list_drone['drone_id']==2) {
                                                $mnview = "_esri";
                                            } else {
                                                $mnview = "";
                                            }
                                        ?>
                                    <li>
                                        <a href="map<?php echo $mnview; ?>.php?drone_id=<?php echo urlencode($list_drone['drone_id']); ?>">
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
                        <h1 class="page-header"><i class="fa fa-fw fa-desktop"></i> 
                            Dashboard <small>Drone Swarm and Navigation System</small>
                        </h1>                        
                    </div>
                </div>
                
                

                <div class="row">
                    <div class="col-lg-12"><br><br>
                        <div class="table-responsive">
                            <table class="table table-bordered table-hover table-striped">
                                <tr>
                                    <th><i class="fa fa-fw fa-plane"></i>   Drone ID</th>                       
                                    <th><i class="fa fa-fw fa-power-off"></i>   Power</th>
                                    <th><i class="fa fa-battery-three-quarters" aria-hidden="true"></i>   Batt. Status</th>
                                    <th><i class="fa fa-fw fa-anchor"></i>   Avail.</th>
                                    <th><i class="fa fa-fw fa-arrows-v"></i>   Altitude (last known)</th>
                                    <th><i class="fa fa-fw fa-compass"></i>   Heading</th>
                                    <th colspan="4"><center><i class="fa fa-fw fa-tasks"></i>   Action</center></th>
                                </tr>
                                <?php
                                while ($count_drone = mysqli_fetch_assoc($count_result)) { 
                                    echo "<tr>";
                                        echo "<td>".$count_drone['drone_id']."</td>";   
                                        $did = $count_drone['drone_id'];
                                        $progress_id = "myProgress".$count_drone['drone_id'];
                                        $bar_id = "myBar".$count_drone['drone_id'];
                                        $label_id = "label".$count_drone['drone_id'];                           
                                        ?>                               
                                        <td><span style="border: 3px solid #fff; border-radius: 100%; display: inline-block; background: #0EC518; box-shadow: 0 -2px 0 3px #0b9512 inset, 0 5px 5px rgba(0, 7, 1, 0.17), 0 15px rgba(255, 255, 255, 0.25) inset; height: 3vw; width: 3vw;" id="<?php echo "btn".$count_drone['drone_id']; ?>"></span><p id="<?php echo "pow_id".$count_drone['drone_id']; ?>"></p></td> 
                                        <td>
                                            <div style="position: relative; width: 100%; height: 30px; background-color: #ddd;" id="<?php echo $progress_id; ?>">
                                                <div style="position: absolute; height: 100%; background-color: green;" id="<?php echo $bar_id; ?>"><br>
                                                    <div style="text-align: center; line-height: 10px; color: white;" id="<?php echo $label_id; ?>"><p id= "<?php echo "lab_id".$count_drone['drone_id']; ?>"></p></div>
                                                </div>
                                            </div>                              
                                        </td>   
                                        <td><p id="<?php echo "flight_id".$count_drone['drone_id']; ?>"></p></td>
                                        <td><p id="<?php echo "alti_id".$count_drone['drone_id']; ?>"></p></td>
                                        <td><p id="<?php echo "head_id".$count_drone['drone_id']; ?>"></p></td>
                                        <?php
                                            if ($count_drone['drone_id']==2) {
                                                $mview = "_esri";
                                            } else {
                                                $mview = "";
                                            }
                                        ?>
                                        <td><a href="map<?php echo $mview; ?>.php?drone_id=<?php echo urlencode($did); ?>"><i class="fa fa-fw fa-location-arrow"></i>  Track</a></td>
                                        <td><a href="start_trip.php?drone_id=<?php echo urlencode($count_drone['drone_id']); ?>" ><i class="fa fa-fw fa-fighter-jet"></i>   Start a trip</a></td>
                                        <td><a href="trip.php?drone_id=<?php echo urlencode($did); ?>"><i class="fa fa-fw fa-archive"></i>   Trip record</a></td>
                                        <td><a href="#"><i class="fa fa-fw fa-user-secret"></i>   Consumer details</a></td>
                                        <?php                                                   
                                    echo "</tr>";
                                }
                                ?>
                            </table>
                        </div>
                        <br><br><br><br>
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

    <script src="http://code.jquery.com/jquery-latest.js"></script>
     
    <script type="text/javascript">         
        $(document).ready(function() {
            $("#responsecontainer").load("select.php");
            
            var refreshId = setInterval(function() {
                $("#responsecontainer").load('select.php?randval='+ Math.random());
                var x = document.getElementById("responsecontainer").value;         
                var dcount = document.getElementById("dcount").value;
                var loop = dcount*13;               
                var locate = x.split(',');              
                var power = new Array(loop);            
                var batt = new Array(loop);
                //var flight = new Array(loop);
                var d_id = new Array(loop);
                var alti_id = new Array(loop);
                var head_id = new Array(loop);

                var b = 12;
                for (var i = 12; i <loop; i+=13) {
                    power[i-b] = locate[i-8];  
                    batt[i-b] = locate[i-7];
                    //flight[i-b] = locate[i-6];
                    d_id[i-b] = locate[i-9];    
                    alti_id[i-b] = locate[i-10];
                    head_id[i-b] = locate[i-6];     
                    
                    if (batt[i-b]<=12.4) {
                        batt[i-b] = (batt[i-b]/12.4)*100;
                        batt[i-b] = batt[i-b].toFixed(2);
                        //console.log(document.getElementById("lab_id"+d_id[i-b]));
                        document.getElementById("lab_id"+d_id[i-b]).innerHTML = batt[i-b]+"%";  
                    } else if (batt[i-b]>12.4){
                        batt[i-b] = 100;
                        console.log(document.getElementById("lab_id"+d_id[i-b]));
                        //batt_perc = batt_perc.toFixed(2);
                        document.getElementById("lab_id"+d_id[i-b]).innerHTML = batt[i-b]+"%";  
                    }         
                    document.getElementById("alti_id"+d_id[i-b]).innerHTML = alti_id[i-b]+" metres";   
                    document.getElementById("head_id"+d_id[i-b]).innerHTML = head_id[i-b]+" deg. "; 
                    if (power[i-b]==1) {
                        document.getElementById("pow_id"+d_id[i-b]).innerHTML = "ON";     
                        document.getElementById("btn"+d_id[i-b]).style.background = "#0EC518";
                        document.getElementById("btn"+d_id[i-b]).style.boxShadow = "0 -2px 0 3px #0b9512 inset, 0 5px 5px rgba(0, 7, 1, 0.17), 0 15px rgba(255, 255, 255, 0.25) inset";               
                    } else {
                        document.getElementById("pow_id"+d_id[i-b]).innerHTML = "OFF";
                        document.getElementById("btn"+d_id[i-b]).style.background = "#E53030";
                        document.getElementById("btn"+d_id[i-b]).style.boxShadow = "0 -2px 0 3px #c91919 inset, 0 5px 5px rgba(65, 8, 8, 0.17), 0 15px rgba(255, 255, 255, 0.25) inset";
                    }                
                    document.getElementById("myBar"+d_id[i-b]).style.width = batt[i-b]+"%";
                    
                    if ((batt[i-b]<=100)&&(batt[i-b]>=86)) {
                        document.getElementById("myBar"+d_id[i-b]).style.backgroundColor = "green";
                    } else if ((batt[i-b]<86)&&(batt[i-b]>=84)) {
                        document.getElementById("myBar"+d_id[i-b]).style.backgroundColor = "orange";
                    } else if (batt[i-b]<84) {
                        document.getElementById("myBar"+d_id[i-b]).style.backgroundColor = "red";          
                    } 
                    
                    if ((alti_id[i-b]<=1)||(power[i-b]==0)) {
                        document.getElementById("flight_id"+d_id[i-b]).innerHTML = "On ground";
                    } else {
                        document.getElementById("flight_id"+d_id[i-b]).innerHTML = "In air";
                    }  
            
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
