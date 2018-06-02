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

    if (isset($_POST['submit'])) {
        $way_lat = $_POST['lat'];
        $way_lon = $_POST['lon'];       
        $opt = $_POST['opt'];
        $opt_s = implode("_", $opt);
        $camera_in = $_POST['camera_in'];
        $camera_s = implode("_", $camera_in);
        $query = "UPDATE trips SET way_lat = '{$way_lat}', way_lon = '{$way_lon}' WHERE trip_id = {$trip_id} AND did = {$did} LIMIT 1";
        mysqli_query($conn, $query);
        $count_query = "SELECT * FROM trips WHERE trip_id = {$trip_id} LIMIT 1";
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

            $query = "INSERT INTO ways (did, trip_id, hub, way_lati, way_long, altitude, opt, camera) VALUES ({$did}, {$trip_id}, '{$current_user}', '{$way_lati[$i]}', '{$way_long[$i]}', '{$altitude}', {$opt[$i]}, {$camera_in[$i]})";
            mysqli_query($conn, $query);
        }

        //redirect_to("http://drone".$did.".ngrok.io/cns/public/intest.php?trip_id=$trip_id&drone_id=$did&way_lat=$way_lat&way_lon=$way_lon&altitude=$altitude&opt_s=$opt_s&camera_s=$camera_s&camera_type=$camera_type");

        redirect_to("map_esri.php?drone_id=$did");
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
    <link rel="stylesheet" href="https://js.arcgis.com/4.2/esri/css/main.css">
   
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
    <style type="text/css">
        #viewDiv {
          padding: 0;
          margin: 0;
          height: 100%;
          width: 100%;
        }    
        #info {
          background-color: black;
          opacity: 0.75;
          color: orange;
          font-size: 18pt;
          padding: 8px;
          visibility: hidden;
        }
    </style>

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
                                Click on the make route button to plot a route and then click above the route to make waypoints. Click on the start trip button after crearing the waypoints. 
                            </li>                            
                        </ol>
                    </div>
                </div>
                <!-- /.row -->
                 
                <input type="button" class="btn btn-lg btn-primary" style="display:none;" id="routebtn" value="Make route" /> 
                <textarea style="display:none;" id="last_lati"><?php  echo $last_pos['latitude']; ?></textarea>
                <textarea style="display:none;" id="last_longi"><?php  echo $last_pos['longitude']; ?></textarea>
                <div class="row">  
                    <div onclick="addCod('cod');" class="col-lg-6">
                        <div onclick="addCamera('camera_box');">                             
                            <div onclick="addSelect('select-container');">
                                <div id="viewDiv"></div>                                
                            </div>
                        </div>
                    </div>
                     <div class="col-lg-6">
                        <div id="cod" class="col-lg-4">
                            <h4>Waypoint</h4><hr>
                        </div>
                        <div>
                        
                            <form method="post" action="waypoints.php?trip_id=<?php echo $trip_id; ?>&drone_id=<?php echo $did; ?>">
                                <textarea style="display:none;"  id="lat" name="lat"></textarea>
                                <textarea style="display:none;" id="lon" name="lon"></textarea>  
                                <div class="col-lg-4" id="select-container">
                                    <h4>Action</h4><hr>
                                </div>
                                 <div class="col-lg-4" id="camera_box">
                                    <h4>Camera</h4><hr>    
                                </div>  
                                <input type="submit" class="btn btn-lg btn-success" name="submit" value="Start flight">
                            </form>
                        </div>  
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
    
    function addSelect(divname) {
        var newDiv=document.createElement('div');
        newDiv.setAttribute("class", "form-group");
        var html = '<select name="opt[]" class="form-control">';                     
        html += "<option value='2'>Waypoint</option>";
        html += "<option value='1'>Take-off</option>";
        html += "<option value='3'>Landing</option>";
        html += '</select>';
        newDiv.innerHTML= html;
        document.getElementById(divname).appendChild(newDiv);        
    }
    function addCamera(divname) {
        var cameraDiv=document.createElement('div');
        cameraDiv.setAttribute("class", "form-group");
        var camerahtml = '<select name="camera_in[]" class="form-control">';                     
        camerahtml += "<option value='0'>No click</option>";
        camerahtml += "<option value='1'>Single click</option>";
        camerahtml += "<option value='2'>Continous click</option>";
        camerahtml += '</select>';
        cameraDiv.innerHTML= camerahtml;
        document.getElementById(divname).appendChild(cameraDiv);        
    }
    var i = 0;  
    function addCod(divname) {
        var codDiv=document.createElement('div');
        codDiv.setAttribute("class", "form-group");
        var codlabels = ['A','B','C','D','E','F','G','H','I','J','K','L','M','N','O','P','Q','R','S','T','U','V','W','X','Y','Z'];                     
        codDiv.innerHTML= "<h3>Point "+codlabels[i]+"</h3>";
        document.getElementById(divname).appendChild(codDiv);
        i = i + 1; 
    }           
    </script>    
     <script src="https://js.arcgis.com/4.2/"></script>

    <script type="text/javascript">
        require([
            "esri/Map",
            "esri/views/MapView",
            "esri/Graphic",
            "esri/geometry/Point",
            "esri/geometry/Polyline",
            "esri/geometry/Polygon",
            "esri/symbols/TextSymbol",
            "esri/symbols/SimpleMarkerSymbol",
            "esri/symbols/SimpleLineSymbol",
            "esri/symbols/SimpleFillSymbol",
            "dojo/domReady!"
        ], function(
            Map, MapView,
            Graphic, Point, Polyline, Polygon, TextSymbol,
            SimpleMarkerSymbol, SimpleLineSymbol, SimpleFillSymbol
        ) {     

            var map = new Map({
            basemap: "satellite"
        });
        var last_lati = document.getElementById("last_lati").value;
        var last_longi = document.getElementById("last_longi").value;
        var view = new MapView({
            center: [last_longi, last_lati],
            container: "viewDiv",
            map: map,
            zoom: 18
        });
        
        var point = new Point({
            longitude: last_longi,
            latitude: last_lati
        });

        // Create a symbol for drawing the point
        var markerSymbol = new SimpleMarkerSymbol({
            color: [129, 219, 40],
            outline: { // autocasts as new SimpleLineSymbol()
                color: [255, 255, 255],
                width: 2
            }
        });

        // Create a graphic and add the geometry and symbol to it
        var pointGraphic = new Graphic({
            geometry: point,
            symbol: markerSymbol
        }); 
  
        // Add the graphics to the view's graphics layer
        view.graphics.add(pointGraphic);    
        var last_loc2 = last_longi;
        var last_loc1 = last_lati;

        view.on("click", function(evt) {
            //debugger
            document.getElementById("lat").innerHTML = evt.mapPoint.latitude;
            document.getElementById("lat").value += "_" + evt.mapPoint.latitude;
            document.getElementById("lon").innerHTML = evt.mapPoint.longitude;
            document.getElementById("lon").value += "_" + evt.mapPoint.longitude;

            var point = new Point({
                longitude: evt.mapPoint.longitude,
                latitude: evt.mapPoint.latitude
            });

            // Create a symbol for drawing the point
            var markerSymbol = new SimpleMarkerSymbol({
                color: [226, 119, 40],                
                outline: { // autocasts as new SimpleLineSymbol()
                    color: [255, 255, 255],
                    width: 2
                }
            });

            // Create a graphic and add the geometry and symbol to it
            var pointGraphic = new Graphic({
                geometry: point,
                symbol: markerSymbol
            });

            // First create a line geometry (this is the Keystone pipeline)
            var polyline = new Polyline({
                paths: [[last_loc2, last_loc1] ,[evt.mapPoint.longitude, evt.mapPoint.latitude]]
            });

            // Create a symbol for drawing the line
            var lineSymbol = new SimpleLineSymbol({
                color: [226, 119, 40],
                width: 4
            });

            // Create an object for storing attributes related to the line
            var lineAtt = {
                Name: "Waypoints",
                Owner: "R2 Robotronics"                
            };
              
            var polylineGraphic = new Graphic({
                geometry: polyline,
                symbol: lineSymbol,
                attributes: lineAtt,
                popupTemplate: { // autocasts as new PopupTemplate()
                    title: "{Name}",
                    content: [{
                        type: "fields",
                        fieldInfos: [{
                            fieldName: "Name"
                        }, {
                            fieldName: "Owner"
                        }, {
                            fieldName: "Length"
                        }]
                    }]
                }
            });    
            //var mapPoint = new Esri.ArcGISRuntime.Geometry.MapPoint(evt.mapPoint.longitude, evt.mapPoint.latitude);
            
            // Add the graphics to the view's graphics layer
            
            view.graphics.add(pointGraphic);              
            view.graphics.add(polylineGraphic);  
           // view.graphics.add(textGraphic); 
            //view.graphics.add(new esri.Graphic(point, new esri.symbol.TextSymbol(counter).setOffset(0,12)));
            last_loc2 = evt.mapPoint.longitude;
            last_loc1 = evt.mapPoint.latitude;                        
        });
    });
  </script>
</body>

</html>
<?php
if (isset ($conn)){
    mysqli_close($conn);
}
?>