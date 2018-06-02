<?php
	$lat = "0";
	$lon = "0";
	$placeName = "";
	if (isset($_GET['lat'])) { $lat = $_GET['lat'];	} else { $lat = "0"; }
	if (isset($_GET['lon'])) { $lon = $_GET['lon'];	} else { $lon = "0"; }
	if (isset($_GET['placeName'])) { $placeName = $_GET['placeName'];	} else { $placeName = ""; }
?>
<!DOCTYPE html>
<html>
<head>
	<title></title>
</head>
<input type="text" id="pop" name="lat" onchange="showPop()">
<body>
<script type="text/javascript">
	function showPop(){
		alert(document.getElementById("pop").value);
	}
</script>
</body>
</html>