<?php require_once("includes/db_connection.php");?>
<!DOCTYPE html>
<html lang="en">

<head>
    <script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyC8hbCo346Mcq6rHyTE3Niwn5gVhaWwKcs"></script>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.3.1/jquery.js"></script>

</head>

<body> 
    <table>
        <tr>
            <td><div id="googleMap" style="width:1410px;height:780px;"></div></td>  
        </tr>
    </table>
    <br><br>  
    <textarea style="display: none;" id="responsecontainer" ></textarea>
    <script>   
        document.body.style.backgroundColor = "#87cefa";
        
        var locate = [];

        function initialize()
        {        
            var marker;     
            var marker_info;
            var mapProp = { 
                center: new google.maps.LatLng(0,0),        
                zoom:18,
                mapTypeId: 'roadmap'
            };  
            var map = new google.maps.Map(document.getElementById("googleMap"),mapProp);
            var image = {
                url: 'http://medicento.com/gmpAlexa/markIco.png',
                size: new google.maps.Size(20, 32)
              };
            $(document).ready(function() {
                $("#responsecontainer").load("select.php");
                var refreshId = setInterval(function() {
                    $("#responsecontainer").load('select.php?randval='+ Math.random());
                    var x = document.getElementById("responsecontainer").value;
                    locate = x.split(','); 
                    if (locate[3] == "0") {
                        marker.setMap(null);
                    } else {
                        document.getElementById("googleMap").style.zIndex = "-1000000";
                        var position=new google.maps.LatLng(locate[0], locate[1]);       
                        if(marker != null){
                            marker.setMap(null);          
                        }
                        marker = new google.maps.Marker({
                            position: position,
                            map: map,
                            icon: image            
                        });
                        map.setCenter(position);    
                        marker_info = new google.maps.InfoWindow({
                          content: locate[2]
                        }); 
                        marker_info.open(map,marker);  
                        
                    }
                }, 1000);
                $.ajaxSetup({ cache: false}); 
            });
        }
        google.maps.event.addDomListener(window, 'load', initialize);        
    </script>  

</body>

</html>
