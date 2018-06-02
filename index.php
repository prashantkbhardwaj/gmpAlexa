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
            <td><div id="googleMap" style="width:600px;height:480px;"></div></td>  
        </tr>
    </table>
    <br><br>  
    <textarea id="responsecontainer" ></textarea>
    <script>   
        $("#responsecontainer").load("select.php");
        var temp = false;
        var locate = [];
        var refreshId = setInterval(function() {
            $("#responsecontainer").load('select.php?randval='+ Math.random());
            var x = document.getElementById("responsecontainer").value;
            locate = x.split(','); 
            if (locate[3] == "0") {
                document.body.style.backgroundColor = "#87cefa";
            } else {
                document.body.style.backgroundColor = "white";
                temp = true;
                
            }
        }, 1000);
        $.ajaxSetup({ cache: false});  

        $('#responsecontainer').bind('input', function() {
            alert("changed")
            initialize(locate[0], locate[1], locate[2]);
        } );

        function initialize(lat, lon, placeName)
        {        
            var marker;     
            var marker_info;
            var mapProp = { 
                center: new google.maps.LatLng(0,0),        
                zoom:18,
                mapTypeId: 'roadmap'
            };  
            var map = new google.maps.Map(document.getElementById("googleMap"),mapProp);
            var position=new google.maps.LatLng(lat, lon);       
            if(marker != null){
                marker.setMap(null);          
            }
            marker = new google.maps.Marker({
                position: position,
                map: map,            
            });
            map.setCenter(position);    
            marker_info = new google.maps.InfoWindow({
              content: placeName
            }); 
            marker_info.open(map,marker);  
        }
               
    </script>  

</body>

</html>
