<?php require_once("../includes/session.php");?>
<?php require_once("../includes/db_connection.php");?>
<?php require_once("../includes/functions.php");?>
<?php // confirm_logged_in(); ?>
<?php
    $current_user = $_SESSION["username"];
    $name_query = "SELECT * FROM hubs WHERE username = '{$current_user}' LIMIT 1";
    $name_result = mysqli_query($conn, $name_query);
    confirm_query($name_result);
    $name_title = mysqli_fetch_assoc($name_result);
?>
<?php 
    
    $drone_id = $_GET["drone_id"];
?>
<?php
    $count = "SELECT count( DISTINCT(drone_id) ) FROM pings";
    $count_result = mysqli_query($conn, $count);
    confirm_query($count_result);
    $row = mysqli_fetch_array($count_result);
    $total_drones = $row[0];    
?>
<?php
    if (isset($_POST['submit'])) {
        
        $ecall = $_POST['ecall'];
        
        $query = "UPDATE calls SET ecall = '{$ecall}' WHERE drone_id = {$drone_id} LIMIT 1";
        $result = mysqli_query($conn, $query);
        confirm_query($result);    
        //redirect_to("https://b805596b.ngrok.io/cloud_nav_sys/software/public/ecall.php?drone_id=$drone_id&ecall=1");
        //redirect_to("map.php?drone_id=35");
    }   
    $equery = "SELECT * FROM calls WHERE drone_id = {$drone_id} LIMIT 1";
    $eresult = mysqli_query($conn, $equery);
    confirm_query($eresult);
    $etitle = mysqli_fetch_assoc($eresult);
    $vals = $etitle['ecall'];  
    if ($vals==1) {
        $bttn = "disabled";
    } elseif ($vals==0) {
        $bttn = "";
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
    <style type="text/css">
        body{
            background: url("images/world.jpg") no-repeat center center fixed;      
            -webkit-background-size: cover;
            -moz-background-size: cover;
            -o-background-size: cover;
            background-size: cover;
        }
        #alert_box {
            width: 450px;
            height: 250px;
            margin-top: 10%; 
            margin-left: 33%;
            background: url("images/alert.png");      
            -webkit-background-size: cover;
            -moz-background-size: cover;
            -o-background-size: cover;
            background-size: cover;
            position: absolute;
            z-index: 1;
            display: none;
            color: white;
            box-shadow: 0px 0px 60px 20px red;
            /* in order: x offset, y offset, blur size, spread size, color */
            /* blur size and spread size are optional (they default to 0) */
        }
    </style>

    <!-- Bootstrap Core CSS -->
    <link href="css/bootstrap.min.css" rel="stylesheet">

    <!-- Custom CSS -->
    <link href="css/sb-admin.css" rel="stylesheet">

    <!-- Custom Fonts -->
    <link href="font-awesome/css/font-awesome.min.css" rel="stylesheet" type="text/css">

    <link href="css/style.css" rel="stylesheet" type="text/css" />    
    <script src="js/mscorlib.js" type="text/javascript"></script>
    <script src="js/PerfectWidgets.js" type="text/javascript"></script>
    <script src="js/widgets_data.js" type="text/javascript"></script>

    <script src="http://code.jquery.com/jquery-latest.js"></script>

    <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
    <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
    <!--[if lt IE 9]>
        <script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
        <script src="https://oss.maxcdn.com/libs/respond.js/1.4.2/respond.min.js"></script>
    <![endif]-->

</head>

<body onload="initialize()"> 

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
                <a href="index.php"><i class="fa fa-home"></i></a>                    
            </li>                
            <li class="dropdown">
                <a href="logout.php"><i class="fa fa-sign-out"></i></a>                    
            </li>
            <li class="dropdown">
                <a href="#"><i class="fa fa-user"></i> <?php echo htmlentities($name_title["username"]); ?> </a>                   
            </li>
        </ul>  
    </nav>
    <audio id="myAudio">
      <source src="media/alert.mp3" type="audio/mpeg">  
    </audio>
    <div id="alert_box">
        <center><br><br><br><h1>Caution!<br>Angle too sharp!</h1></center>
    </div>
    <table>
        <tr>
            <td><div id="viewDiv" style="width:600px;height:480px;"></div></td>       
            <td>
                <div id="gwrapper">
                    <div class="gcontainer" id="row-one">
                        <div class="svg quad" id="airSpeed"></div>
                        <div class="svg quad" id="altimeter"></div>
                        <div class="svg quad" id="attitudeIndicator"></div>
                    </div>
                    <div class="gcontainer" id="row-two">
                        <div class="svg quad" id="headingIndicator"></div>
                        <div class="svg quad" id="turnCoordinator"></div>
                        <div class="svg quad" id="verticalSpeedIndicator"></div>
                    </div>       
                </div>
            </td>
        </tr>
        <tr>
            <td>
                 <form method="post" action="map.php?drone_id=<?php echo $drone_id; ?>">
                    <input type="number" name="ecall" value="1" style="display:none;">
                    <center>
                        <input type="submit" class="btn btn-lg btn-danger" name="submit" value="Emergency recall" <?php echo $bttn; ?> >
                    </center>
                </form>
            </td>
            <td>
                <div style="position: relative; width: 100%; height: 30px; background-color: #ddd;" id="prog">
                    <div style="position: absolute; height: 100%; background-color: green;" id="bar">
                        <div style="text-align: center; line-height: 6px; color: white;" id="lab"><br><br><p id= "lab_num"></p></div>
                    </div>
                </div>      
            </td>
        </tr>   
    </table>
    <br><br>   
    <textarea style="display:none;" id="dcount" ><?php echo $total_drones; ?></textarea>
    <textarea style="display:none;" id="did" ><?php echo $drone_id; ?></textarea>
    <textarea style="display:none;" id="responsecontainer" ></textarea>
    <textarea style="display:none;" id="alti" ></textarea>
    <textarea style="display:none;" id="batt" ></textarea>
    <textarea style="display:none;" id="pitch" ></textarea>
    <textarea style="display:none;" id="roll" ></textarea>
    <textarea style="display:none;" id="airspeed" ></textarea>
    <textarea style="display:none;" id="head"></textarea>
    <textarea style="display:none;" id="bank" ></textarea>
    <textarea style="display:none;" id="vert_speed" ></textarea>
    <script src="https://js.arcgis.com/4.2/"></script>
    <script>    
        function initialize()
        {    var point;
                var graphic;
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
                "esri/Color",
                "dojo/domReady!"
            ], function(
                Map, MapView,
                Graphic, Point, Polyline, Polygon, TextSymbol, Color,
                SimpleMarkerSymbol, SimpleLineSymbol, SimpleFillSymbol
            ) {     

                var map = new Map({
                    basemap: "satellite"
                });
                var view = new MapView({
                    center: [0, 0],
                    container: "viewDiv",
                    map: map,
                    zoom: 18
                });     
               
                

                // Create a graphic and add the geometry and symbol to it
                var pointGraphic;
                $(document).ready(function() {
                    $("#responsecontainer").load("select.php");
                    var refreshId = setInterval(function() {
                        $("#responsecontainer").load('select.php?randval='+ Math.random());
                        var x = document.getElementById("responsecontainer").value;
                        var drone = document.getElementById("did").value;
                        var dcount = document.getElementById("dcount").value;
                        var loop = dcount*12;               
                        var locate = x.split(',');
                        
                        for (var i = 11; i < loop; i+=12) {
                            
                            if (locate[i-8]==drone) {
                                //var position=new google.maps.LatLng(locate[i-11], locate[i-10]);        
                                document.getElementById("alti").innerHTML = locate[i-9];
                                var batt_val = locate[i-6];
                                if (batt_val<=12.4) {
                                    var batt_perc = (batt_val/12.4)*100;
                                    batt_perc = batt_perc.toFixed(2);
                                    document.getElementById("batt").innerHTML = batt_perc;
                                    document.getElementById("lab_num").innerHTML = batt_perc+" %";
                                } else if (batt_val>12.4){
                                    var batt_perc = 100;
                                    //batt_perc = batt_perc.toFixed(2);
                                    document.getElementById("batt").innerHTML = batt_perc;
                                    document.getElementById("lab_num").innerHTML = batt_perc+" %";
                                }                       
                                                
                                document.getElementById("head").innerHTML = locate[i-5];
                                document.getElementById("pitch").innerHTML = locate[i-4];
                                document.getElementById("roll").innerHTML = locate[i-3];
                                document.getElementById("airspeed").innerHTML = locate[i-2];
                                document.getElementById("bank").innerHTML = locate[i-1];
                                document.getElementById("vert_speed").innerHTML = locate[i];  

                                point = new Point(locate[i-10], locate[i-11]); 
                                if (!graphic) {
                                    addGraphic(point);
                                } else {
                                    graphic.setGeometry(point);
                                }
                                map.centerAt(point);
                                function addGraphic(point){
                                  var symbol = new SimpleMarkerSymbol(
                                    SimpleMarkerSymbol.STYLE_CIRCLE, 
                                    12, 
                                    new SimpleLineSymbol(
                                      SimpleLineSymbol.STYLE_SOLID,
                                      new Color([210, 105, 30, 0.5]), 
                                      8
                                    ), 
                                    new Color([210, 105, 30, 0.9])
                                  );
                                  graphic = new Graphic(point, symbol);
                                  map.graphics.add(graphic);
                                }
                            };              
                        }; 
                                                             
                    }, 1000);
                    $.ajaxSetup({ cache: false});       
                });
            }); 
        }
        //google.maps.event.addDomListener(window, 'load', initialize);       
    </script>   
    <script type="text/javascript">
        var timeoutId;
        var airSpeedWidget;
        var airSpeedSlider;

        var altimeterWidget;
        var heightSlider;

        var attitudeIndicatorWidget;
        var pitchSlider;
        var rollSlider;

        var headingIndicatorWidget;
        var directionSlider;

        var turnCoordinatorWidget;
        var turnRollSlider;
        var slipSlider;

        var verticalSpeedIndicatorWidget;
        var verticalSpeedSlider;
        var timeOutMiliseconds = 10;

        var pitchValue = 0;
        var rollValue = 0;

        function nextInt(minValue, maxValue) {
            return Math.floor((Math.random() * (maxValue - minValue)) + minValue);
        }

        function nextDouble(minValue, maxValue) {
            return ((Math.random() * (maxValue - minValue)) + minValue);
        }

        function reloadWidget() {
            //widget model
            //initialized in widget_data.js

            //creating widget
            airSpeedWidget = new PerfectWidgets.Widget("airSpeed", jsonModel1);
            altimeterWidget = new PerfectWidgets.Widget("altimeter", jsonModel2);
            attitudeIndicatorWidget = new PerfectWidgets.Widget("attitudeIndicator", jsonModel3);
            headingIndicatorWidget = new PerfectWidgets.Widget("headingIndicator", jsonModel4);
            turnCoordinatorWidget = new PerfectWidgets.Widget("turnCoordinator", jsonModel5);
            verticalSpeedIndicatorWidget = new PerfectWidgets.Widget("verticalSpeedIndicator", jsonModel6);

            window.onresize = function(event) {
                airSpeedWidget.rescale();
                altimeterWidget.rescale();
                attitudeIndicatorWidget.rescale();
                headingIndicatorWidget.rescale();
                turnCoordinatorWidget.rescale();
                verticalSpeedIndicatorWidget.rescale();
            }

            grabAttitudeIndicatorSliders();
            grabAltimeterSliders();
            grabAirSpeedWidgetSliders();
            grabHeadingIndicatorSliders();
            grabTurnCoordinatorSliders();
            grabVerticalSpeedSliders();
            timeoutId = window.setTimeout(updateInstruments, timeOutMiliseconds);
        }

        function grabVerticalSpeedSliders() {
            verticalSpeedSlider = verticalSpeedIndicatorWidget.getByName("Slider2");
            verticalSpeedSlider.configureAnimation({ "enabled": true, "ease": "swing", "duration": 20 });
            verticalSpeedSlider.addAnimationValueChangedHandler(verticalSpeedChangedHandler);
        }

        function verticalSpeedChangedHandler(sender, e) {
            verticalSpeedIndicatorWidget.getByName("Slider1").recalculate();
        }

        function grabAltimeterSliders() {
            heightSlider = altimeterWidget.getByName("height");
            heightSlider.configureAnimation({ "enabled": true, "ease": "swing", "duration": 10 });
            heightSlider.addAnimationValueChangedHandler(heightMovementHandler);
        }

        function heightMovementHandler(sender, e) {
            altimeterWidget.getByName("Slider1").recalculate();
            altimeterWidget.getByName("Slider2").recalculate();
        }

        function grabHeadingIndicatorSliders() {
            directionSlider = headingIndicatorWidget.getByName("Slider1");
            directionSlider.configureAnimation({ "enabled": true, "ease": "swing", "duration": 10 });
        }

        function grabAttitudeIndicatorSliders() {
            pitch = attitudeIndicatorWidget.getByName("Pitch");
            rollSlider = attitudeIndicatorWidget.getByName("Roll");
            rollSlider.configureAnimation({ "enabled": true, "ease": "swing", "duration": 10 });
        }

        function grabTurnCoordinatorSliders() {
            slipSlider = turnCoordinatorWidget.getByName("Slider2");
            turnRollSlider = turnCoordinatorWidget.getByName("Slider1");
            turnRollSlider.configureAnimation({ "enabled": true, "ease": "swing", "duration": 2 });
        }

    function grabAirSpeedWidgetSliders() {
        airSpeedSlider = airSpeedWidget.getByName("Speed");
        airSpeedSlider.configureAnimation({ "enabled": true, "ease": "swing", "duration": 20 });
        airSpeedSlider.addAnimationValueChangedHandler(airSpeedMovementHandler);
    }

    function airSpeedMovementHandler(sender, e) {
        airSpeedWidget.getByName("Slider2").recalculate();
    } //

    function updateInstruments() {
        //clearTimeout(timeoutId);
        setInterval(function(){
            pitchValue = document.getElementById("pitch").value;
            var newRoll = document.getElementById("roll").value;
            //var x = 360;    
            var alti = 100*document.getElementById("alti").value;
            var airspeed = document.getElementById("airspeed").value;
            var bank = document.getElementById("bank").value;
            var vert_speed = document.getElementById("vert_speed").value;
            var x = document.getElementById("head").value;
            if ((x >= 0) && (x < 225)) {
                directionSlider.setValue(225 - x);
            } else if ((x >= 225) && (x < 360)) {
                directionSlider.setValue(360 - (x - 225));
            } else if (x>359) {
                directionSlider.setValue(225);
            }

            if ((newRoll>=30)||(newRoll<=-30)) {
                document.getElementById("myAudio").play();
                document.getElementById("alert_box").style.display = "initial";
            } else {
                document.getElementById("myAudio").pause();
                document.getElementById("alert_box").style.display = "none";            
            }
            var batt = document.getElementById("batt").value;
            document.getElementById("bar").style.width = batt+"%";
            if ((batt<=100)&&(batt>=86)) {
                document.getElementById("myAudio").pause();
                document.getElementById("bar").style.backgroundColor = "green";
            } else if ((batt<86)&&(batt>=84)) {
                document.getElementById("myAudio").pause();
                document.getElementById("bar").style.backgroundColor = "orange";
            } else if (batt<84) {
                document.getElementById("myAudio").play();
                document.getElementById("bar").style.backgroundColor = "red";           
            } 

            if (Math.abs(newRoll) <= 90) {
                rollValue = newRoll;
            }

            turnRollSlider.setValue(rollValue);
            rollSlider.setValue(rollValue);
            pitch.setValue(pitchValue);

            heightSlider.setValue(alti);
            //directionSlider.setValue(x);
            airSpeedSlider.setValue(airspeed);
            slipSlider.setValue(bank);
            verticalSpeedSlider.setValue(vert_speed);
        }, 1000);
            //timeoutId = window.setTimeout(updateInstruments,timeOutMiliseconds);
    }

    window.addEventListener('load', function() { reloadWidget(); }, false);

    </script>


    <!-- jQuery -->
    <script src="js/jquery.js"></script>

    <!-- Bootstrap Core JavaScript -->
    <script src="js/bootstrap.min.js"></script>

</body>

</html>
<?php
if (isset ($conn)){
    mysqli_close($conn);
}
?>
