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
    
    $trip_id = $_GET['trip_id'];
    $did = $_GET['drone_id'];    
    echo "ui";
    $last_query = "SELECT * FROM pings WHERE drone_id = {$did} ORDER BY id DESC LIMIT 1";
    $last_result = mysqli_query($conn, $last_query);
    confirm_query($last_result);
    $last_pos = mysqli_fetch_assoc($last_result);

    $alti_query = "SELECT altitude FROM trips WHERE trip_id = '{$trip_id}' LIMIT 1";
    $alti_result = mysqli_query($conn, $alti_query);
    confirm_query($alti_result);
    $list_alti = mysqli_fetch_assoc($alti_result);

    if (isset($_POST['submit'])) {
        $way_lat = $_POST['lat_path'];
        $way_lon = $_POST['lon_path'];       
        //$opt = $_POST['opt'];
        //$opt_s = "0";
        //$camera_in = $_POST['camera_in'];
        //$camera_s = "0";
        $query = "UPDATE trips SET way_lat = '{$way_lat}', way_lon = '{$way_lon}' WHERE trip_id = '{$trip_id}' AND did = {$did} LIMIT 1";
        mysqli_query($conn, $query);
        $count_query = "SELECT * FROM trips WHERE trip_id = '{$trip_id}' LIMIT 1";
        $count_result = mysqli_query($conn, $count_query);
        confirm_query($count_result);
        $count_title = mysqli_fetch_assoc($count_result);
        $lats = $count_title['way_lat'];  
        $lons = $count_title['way_lon'];  
        $altitude = $count_title['altitude'];
        $camera_type = $count_title['camera_type'];
        $latis = explode('_' , $lats);  
        $longs = explode('_', $lons);   
        $num_waypoints = count($latis);
        for ($i=0; $i < $num_waypoints-1; $i++) { 
            $way_lati[$i] = $latis[$i+1];
            $way_long[$i] = $longs[$i+1];
            //$dest = $e_dest[0].", ".$e_dest[1];  

            $query = "INSERT INTO ways (did, trip_id, hub, way_lati, way_long, altitude, opt, camera) VALUES ({$did}, {$trip_id}, '{$current_user}', '{$way_lati[$i]}', '{$way_long[$i]}', '{$altitude}', '0', '0')";
            mysqli_query($conn, $query);
            $opt_s = $opt_s.'0_';
            $camera_s = $camera_s.'0_';
        }

        redirect_to("http://drone".$did.".ngrok.io/cns/public/intest.php?trip_id=$trip_id&drone_id=$did&way_lat=$way_lat&way_lon=$way_lon&altitude=$altitude&opt_s=$opt_s&camera_s=$camera_s&camera_type=$camera_type");

        //redirect_to("pending.php?did=".$did."&trip_id=".$trip_id);
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
    <link href="css/bootstrap-datetimepicker.min.css" rel="stylesheet" media="screen">
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
                        <ol class="breadcrumb">
                            <i class="fa fa-fw fa-map-marker"></i>
                            <li>
                                Please click on the boundaries of the plot for making the automatic flight path. Make sure you cover a larger area while plotting the boundries to  avoid errors. Please do not click twice on a boundary, the polygon will be automatically joining its last vertex to the first vertex.
                            </li>                            
                        </ol>
                    </div>
                </div>
                <!-- /.row -->

                <input type="button" class="btn btn-lg btn-primary" style="display:none;" id="routebtn" value="Make route" /> 
                <textarea style="display:none;" id="last_lati"><?php  echo $last_pos['latitude']; ?></textarea>
                <textarea style="display:none;" id="last_longi"><?php  echo $last_pos['longitude']; ?></textarea>
                <textarea style="display: none;" id="altiLine"><?php echo $list_alti['altitude']; ?></textarea>
                <div class="row">  
                    <div class="col-lg-12">
                        <div>
                            <div  style="height:70%; width:100%;" id="map-canvas"></div>
                        </div>
                    </div>
                </div><br>
                <div class="row">
                    <div class="col-lg-12 text-center">
                        <form method="post" action="polyway.php?trip_id=<?php echo $trip_id; ?>&drone_id=<?php echo $did; ?>">
                            <textarea style="display:none;"  id="lat" name="lat"></textarea>
                            <textarea style="display:none;" id="lon" name="lon"></textarea>
                            <textarea style="display:none;"  id="lat_path" name="lat_path"></textarea>
                            <textarea style="display:none;" id="lon_path" name="lon_path"></textarea>
                            <input type="submit" class="btn btn-lg btn-success" id="startbttn" style="display: none;" name="submit" value="Start flight">
                        </form>
                        <div id="div2" style="display: none;">
                            <button class="btn btn-lg btn-success" data-toggle="modal" data-target="#reqfl">Request for flight approval</button>
                        </div>
                        <div id="div1">
                            <button class="btn btn-lg btn-primary" id="preview" onclick="codVal();">Create automatic flight path</button>
                        </div>
                    </div>
                </div><br><br>

            </div>
            <!-- /.container-fluid -->

        </div>
        <!-- /#page-wrapper -->

    </div>
    <!-- /#wrapper -->

    <div class="modal fade" id="reqfl" role="dialog">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                    <h4 class="modal-title">Please select date and time for the flight.</h4>
                </div>
                <div class="modal-body">
                    <p>
                        <form role="form">
                            <div class="row">
                                <div class="col-lg-12">
                                    <div class="form-group">
                                        <div class="input-append date form_datetime" data-date="2012-12-21T15:25:00Z">
                                            <input size="16" type="text" id="flight_time" class="form-control" placeholder="Click to select the date and time" readonly>
                                            <span class="add-on"><i class="icon-th"></i></span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <input type="hidden" id="did" value="<?php echo $did; ?>">
                            <input type="hidden" id="trip_id" value="<?php echo $trip_id; ?>">
                            <div class="row">
                                <div class="col-lg-12 text-center" id="btt1">
                                    <div class="form-group">
                                        <input type="button" id="flightreq" class="btn btn-lg btn-primary" value="Submit flight time">
                                    </div>
                                </div>
                                <div class="col-lg-12 text-center" id="btt2" style="display: none;">
                                    <div class="form-group">
                                        <input type="button" class="btn btn-lg btn-success" value="Send request" onclick="document.getElementById('startbttn').click();">
                                    </div>
                                </div>
                            </div>
                        </form>                      
                    </p>
                </div>
            </div>          
        </div>
    </div>

    <!-- jQuery -->
    <script src="js/jquery.js"></script>

    <!-- Bootstrap Core JavaScript -->
    <script src="js/bootstrap.min.js"></script>
    <!-- <script src="https://maps.googleapis.com/maps/api/js?v=3.exp&sensor=false"></script> -->
    <script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyDGjc9sby68TeD--EaZMovfRE8bRFXtxz8&v=3.exp"></script>
    <script type="text/javascript" src="js/bootstrap-datetimepicker.js" charset="UTF-8"></script>
    <script type="text/javascript" src="js/bootstrap-datetimepicker.fr.js" charset="UTF-8"></script>

    <script type="text/javascript">
        $(document).ready(function() {
            $("#div2").hide();
            $("#btt2").hide();

            $("#preview").click(function() {
              $("#div1").hide();
              $("#div2").show();
            });
        });
    </script>

    <script type="text/javascript">
        $(document).ready(function() {
            $("#flightreq").click(function() {
                var flight_time = $("#flight_time").val();
                var trip_id = $("#trip_id").val();
                var did = $("#did").val();
                $.post("update_flight_time.php", { flight_time1: flight_time, trip_id1: trip_id, did1: did }, function(data) {
                    $("#btt1").hide();
                    $("#btt2").show();
                });
            });
        });
    </script>

    <script type="text/javascript">
        $(".form_datetime").datetimepicker({
            format: "dd MM yyyy - HH:ii P",
            showMeridian: true,
            autoclose: true,
            todayBtn: true
        });
    </script>  

    <script type="text/javascript">  
        var map;
        var last_longi;
        var last_lati;
        last_lati = document.getElementById("last_lati").value;
        last_longi = document.getElementById("last_longi").value;
        var last_loc = new google.maps.LatLng(last_lati, last_longi);
        var mapOptions = {
            zoom: 20,
            mapTypeId: 'satellite',
            center: last_loc
        };    
        map = new google.maps.Map(document.getElementById('map-canvas'), mapOptions);    
        var pos_mar = new google.maps.Marker({
                position: last_loc,
                map: map,
                icon:'images/spot.png'            
            });
        var poly = new google.maps.Polyline({
            strokeColor: '#000000',
            strokeOpacity: 1.0,
            strokeWeight: 3
        });
        poly.setMap(map);
        map.addListener('click', addLatLng);
        function addLatLng(event) {
            var path = poly.getPath();
            path.push(event.latLng); 
        }
        
        google.maps.event.addListener(map, 'click', function(e) {
            document.getElementById("lat").innerHTML = e.latLng.lat();
            document.getElementById("lat").value += "_" + e.latLng.lat();                
            document.getElementById("lon").innerHTML = e.latLng.lng();
            document.getElementById("lon").value += "_" + e.latLng.lng();
        });            
        
        google.maps.event.addListener(map, 'click', function(event) {
            placeMarker(event.latLng);
        });
        var labels = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
        var labelIndex = 0;
        function placeMarker(location) {
            var marker = new google.maps.Marker({
                position: location,
                map: map,                    
                label: labels[labelIndex++ % labels.length],
            });
        } 
    
        function codVal(){
            var lines = [];
            
            var latVal = document.getElementById("lat").value;
            var lonVal = document.getElementById("lon").value;

            var latSplit = latVal.split('_');
            var lonSplit = lonVal.split('_');

            var arrLen = latSplit.length;
            
            var cord = [];
            for (var i = 0; i < arrLen-1; i++) {
                cord[i] = {};
                cord[i]['lat'] = Number(latSplit[i+1]);
                cord[i]['lng'] = Number(lonSplit[i+1]);                
            }
            
            var triangleCoords = cord;

            var bermudaTriangle = new google.maps.Polygon({
                paths: triangleCoords,
                strokeColor: '#FF0000',
                strokeOpacity: 0.8,
                strokeWeight: 3,
                fillColor: '#FF0000',
                fillOpacity: 0.35
            });
            bermudaTriangle.setMap(map);  

            var altiForLine = document.getElementById("altiLine").value;
            var distBetLine;

            if (altiForLine <= 30) {
                distBetLine = 0.00006737333;
            } else if ((altiForLine > 30)&&(altiForLine <= 50)) {
                distBetLine = 0.00011228889;
            } else if ((altiForLine > 50)&&(altiForLine <= 70)) {
                distBetLine = 0.00015720445;
            } else if ((altiForLine > 70)&&(altiForLine <= 90)) {
                distBetLine = 0.00020212001;
            } else if ((altiForLine > 90)&&(altiForLine <= 110)) {
                distBetLine = 0.00024703557;
            } else if ((altiForLine > 110)&&(altiForLine <= 130)) {
                distBetLine = 0.00029195113;
            } else {
                distBetLine = 0.00029195113;
            }

            //path algo starts
            
            
            var waypoint_dynamic = [];
            var n=arrLen-1,id1,id2,i=0,c=0,s=0,j=1,rid,uid,leid,loid,rl1_id,rl2_id;
            var boundary_points = [], lines = [],r = [],u = [],le = [],lo = [],l1 = [],l2 = [],rl1 = [],rl2 = [];

            // std::cout << std::numeric_limits<long double>::digits10 << std::endl;
            //Boundary Points Entry (row indicates id of the point)
            for (var i = 0; i < n; i++) {
                boundary_points.push([Number(latSplit[i+1]),Number(lonSplit[i+1])]);
            }
           /* boundary_points.push([12.84382524360559, 80.15229240059853]);
            boundary_points.push([12.844088060927033, 80.15237957239151]);
            boundary_points.push([12.844099828860376, 80.15221193432808]);
            boundary_points.push([12.843873622934165, 80.15215426683426]); */

            lines.push([-1,-1]);
            for(i=0;i<n-1;i++) {
                var v1 = (boundary_points[i+1][1]-boundary_points[i][1])/(boundary_points[i+1][0]-boundary_points[i][0]);
                var v2 = boundary_points[i][1]-(v1 * boundary_points[i][0]);
                lines.push([v1, v2]);
            }
            var v1 =(boundary_points[0][1]-boundary_points[i][1])/(boundary_points[0][0]-boundary_points[i][0]);
            var v2 =boundary_points[i][1]-(v1*boundary_points[i][0]);
            lines.push([v1, v2]);

            //Rightmost point
            r.push(boundary_points[0][0], boundary_points[0][1]);
            var rid=0;
            for(i=0;i<n;i++)
            {
                if (boundary_points[i][0]>r[0])
                {
                    r[0]=boundary_points[i][0];
                    r[1]=boundary_points[i][1];
                    rid=i;
                }
            }

            //Leftmost point
            le[0] = boundary_points[0][0];
            le[1] = boundary_points[0][1];
            var leid = 0;
            for(i=0;i<n;i++)
            {
                if (boundary_points[i][0]<le[0])
                {
                    le[0]=boundary_points[i][0];
                    le[1]=boundary_points[i][1];
                    leid=i;
                }
            }

            //Uppermost point
            u[0]=boundary_points[0][0];
            u[1]=boundary_points[0][1];
            var uid = 0;
            for(i=0;i<n;i++)
            {
                if (boundary_points[i][1]>u[1])
                {
                    u[0]=boundary_points[i][0];
                    u[1]=boundary_points[i][1];
                    uid=i;
                }
            }

            //Lowermost point
            lo[0]=boundary_points[0][0];
            lo[1]=boundary_points[0][1];
            loid=0;
            for(i=0;i<n;i++)
            {
                if (boundary_points[i][1]<lo[1])
                {
                    lo[0]=boundary_points[i][0];
                    lo[1]=boundary_points[i][1];
                    loid=i;
                }
            }

            var cx;

            //Identify points to the right and left of the rightmost points
            if(rid == 0)
            {
                rl1[0] = boundary_points[n-1][0];
                rl1[1] = boundary_points[n-1][1];
                rl1_id = n-1;
                id1 = n;
            }
            else
            {
                rl1[0] = boundary_points[rid-1][0];
                rl1[1] = boundary_points[rid-1][1];
                rl1_id = rid-1;
                id1 = rid;
            }
            if(rid == n-1)
            {
                id2 = n - 1;
                rl2[0] = boundary_points[0][0];
                rl2[1] = boundary_points[0][1];
                rl2_id = 0;
            }
            else
            {
                id2 = rid + 1;
                rl2[0] = boundary_points[rid+1][0];
                rl2[1] = boundary_points[rid+1][1];
                rl2_id = rid+1;
            }

            //Set difference
            cx=r[0]-distBetLine;

            //cout<<r[0]<<" "<<le[0]<<endl;
            console.log(r[0] + " " + le[0]);
            var number_of_waypoints= (2*(r[0]-le[0])/distBetLine)+20;
            console.log(number_of_waypoints);
            //DynamicArray<float> waypoint_dynamic(number_of_waypoints, 2);
            //Identify active/reference lines
            l1[0] = lines[id1][0];
            l1[1] = lines[id1][1];
            l2[0] = lines[id2][0];
            l2[1] = lines[id2][1];

            while(cx > le[0])
            {
                //calculate waypoints reversing the line each time to get a continuous pattern
                //cout<<cx<<endl;
                if(c%2==0)
                {
                    
                    /*waypoint_dynamic[s][0]=cx;
                    waypoint_dynamic[s][1]=l1[0]*cx+l1[1];*/
                    waypoint_dynamic.push([cx, l1[0]*cx+l1[1]]);
                    s++;
                    waypoint_dynamic.push([cx, l2[0]*cx+l2[1]]);
                    /*waypoint_dynamic[s][0]=cx;
                    waypoint_dynamic[s][1]=l2[0]*cx+l2[1];
                    */
                    s++;
                }
                else
                {
                    /*waypoint_dynamic[s][0]=cx;
                    waypoint_dynamic[s][1]=l2[0]*cx+l2[1];
                    */
                    waypoint_dynamic.push([cx, l2[0]*cx+l2[1]]);
                    s++;
                    /*waypoint_dynamic[s][0]=cx;
                    waypoint_dynamic[s][1]=l1[0]*cx+l1[1];
                    */
                    waypoint_dynamic.push([cx, l1[0]*cx+l1[1]]);
                    s++;
                }
                c++;
                cx = cx - distBetLine;
                //Conditions for replacement of active/reference lines
                if(rl1_id!=rl2_id)
                {
                    if(rl1[0]>=cx)
                    {
                        if(rl1_id==0)
                        {
                            id1 = n;
                            rl1[0]=boundary_points[n-1][0];
                            rl1[1]=boundary_points[n-1][1];
                            rl1_id = n - 1;
                        }
                        else
                        {
                            rl1[0]=boundary_points[rl1_id-1][0];
                            rl1[1]=boundary_points[rl1_id-1][1];
                            rl1_id=rl1_id-1;
                            id1 = id1 - 1;
                        }
                        l1[0]=lines[id1][0];
                        l1[1]=lines[id1][1];
                    }
                    if(rl2[0]>=cx)
                    {
                        if(rl2_id==n-1)
                        {
                            id2 = n;
                            rl2[0]=boundary_points[0][0];
                            rl2[1]=boundary_points[0][1];
                            rl2_id=0;
                        }
                        else
                        {
                            id2=id2+1;
                            rl2[0]=boundary_points[rl2_id+1][0];
                            rl2[1]=boundary_points[rl2_id+1][1];
                            rl2_id=rl2_id+1;
                        }
                        console.log(id2);
                        l2[0]=lines[id2][0];
                        l2[1]=lines[id2][1];
                    }
                }

                //Conditions for replacement of active/reference lines
                
            }
            var path_dynamic = [];
           /* for(var k = 0;k < n;k++){
                document.write(waypoint_dynamic[k][0]);
                //path_dynamic.push({'lat' : waypoint_dynamic[k][0], 'lng': waypoint_dynamic[k][1]});
            }

            window.arr = path_dynamic; */
            
             for(i=0;i<s;i++){
                path_dynamic[i] = {};
                path_dynamic[i]['lat'] = Number(waypoint_dynamic[i][0]);
                path_dynamic[i]['lng'] = Number(waypoint_dynamic[i][1]);
                document.getElementById("lat_path").innerHTML = Number(waypoint_dynamic[i][0]);
                document.getElementById("lat_path").value += "_" + Number(waypoint_dynamic[i][0]);                
                document.getElementById("lon_path").innerHTML = Number(waypoint_dynamic[i][1]);
                document.getElementById("lon_path").value += "_" + Number(waypoint_dynamic[i][1]);
             } /*
            path_dynamic[0] = {'lat': Number(12.844088060927033), 'lng': Number(80.15229240059853)};
            path_dynamic[1] = {'lat': Number(12.844118134533355), 'lng': Number(80.15237957239151)};
            path_dynamic[2] = {'lat': Number(12.843843549298581), 'lng': Number(80.15221193432808)};
            path_dynamic[3] = {'lat': Number(12.843803015262315), 'lng': Number(80.15215426683426)}; */

            var dynamicPath = new google.maps.Polyline({
                path: path_dynamic,
                geodesic: true,
                strokeColor: '#000000',
                strokeOpacity: 1.0,
                strokeWeight: 2
            });

            dynamicPath.setMap(map);
        }
    
</script>    

</body>

</html>
