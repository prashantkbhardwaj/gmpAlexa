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
	$trip_id = $_GET['trip_id'];
	$did = $_GET['did'];

	if (isset($_POST['submit'])) {
		$query = "UPDATE trips SET pilot_status = 1, atc_status = 0 WHERE did = '{$did}' AND trip_id = '{$trip_id}' LIMIT 1";
		$result = mysqli_query($conn, $query);
		confirm_query($result);

		redirect_to("http://drone".$did.".ngrok.io/cns/public/signal.php?signal=1");
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
    <style type="text/css">
        #startbttn {
          background-color: #004A7F;
          -webkit-border-radius: 10px;
          border-radius: 10px;
          border: none;
          color: #FFFFFF;
          cursor: pointer;
          display: inline-block;
          font-family: Arial;
          font-size: 20px;
          padding: 5px 10px;
          text-align: center;
          text-decoration: none;
        }
        @-webkit-keyframes glowing {
          0% { background-color: #B20000; -webkit-box-shadow: 0 0 3px #B20000; }
          50% { background-color: #FF0000; -webkit-box-shadow: 0 0 40px #FF0000; }
          100% { background-color: #B20000; -webkit-box-shadow: 0 0 3px #B20000; }
        }

        @-moz-keyframes glowing {
          0% { background-color: #B20000; -moz-box-shadow: 0 0 3px #B20000; }
          50% { background-color: #FF0000; -moz-box-shadow: 0 0 40px #FF0000; }
          100% { background-color: #B20000; -moz-box-shadow: 0 0 3px #B20000; }
        }

        @-o-keyframes glowing {
          0% { background-color: #B20000; box-shadow: 0 0 3px #B20000; }
          50% { background-color: #FF0000; box-shadow: 0 0 40px #FF0000; }
          100% { background-color: #B20000; box-shadow: 0 0 3px #B20000; }
        }

        @keyframes glowing {
          0% { background-color: #B20000; box-shadow: 0 0 3px #B20000; }
          50% { background-color: #FF0000; box-shadow: 0 0 40px #FF0000; }
          100% { background-color: #B20000; box-shadow: 0 0 3px #B20000; }
        }

        #startbttn {
          -webkit-animation: glowing 1500ms infinite;
          -moz-animation: glowing 1500ms infinite;
          -o-animation: glowing 1500ms infinite;
          animation: glowing 1500ms infinite;
        }
    </style>

    </head>

    <body>

        <div>

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
        	</nav>        
        <div id="page-wrapper" style="background-color: #ECF0F1;">

            <div class="container-fluid">

                <!-- Page Heading -->
                <div class="row">
                    <div class="col-lg-12 text-center">
                        <h1 class="page-header">
                            Waiting for approval from the Air Traffic Control<br>
                            <small>You will be automatically shown the fire button once the flight request is accepted</small>
                        </h1>                        
                    </div>
                </div>

                <input type="hidden" id="trip_id" value="<?php echo $trip_id; ?>">
                <input type="hidden" id="did" value="<?php echo $did; ?>">

                <div class="row">
                    <div class="col-lg-12 text-center">                        
                        <img src="images/load.gif">
                    </div>
                </div>
                <!-- /.row -->

            </div>
            <!-- /.container-fluid -->

        </div>
        <!-- /#page-wrapper -->

    </div>
    <!-- /#wrapper -->
    <div class="modal fade" id="start" role="dialog">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title text-center">Flight request approved</h4>
                </div>
                <div class="modal-body">
                    <p>
                        <div class="row">
                        	<div class="col-lg-12 text-center">
                        		<form method="post" action="pending.php?trip_id=<?php echo $trip_id; ?>&did=<?php echo $did; ?>">
                        			<input type="submit" name="submit" value="Start Flight" id="startbttn">
                        		</form>
                        	</div>
                        </div>                     
                    </p>
                </div>
            </div>          
        </div>
    </div>

    <!-- jQuery -->
    <div id="responsecontainer"></div>
    <script src="js/jquery.js"></script>

    <!-- Bootstrap Core JavaScript -->
    <script src="js/bootstrap.min.js"></script>
    <script type="text/javascript">
        $(document).ready(function() {
        	var trip_id = document.getElementById("trip_id").value;
        	var did = document.getElementById("did").value;
            //$("#responsecontainer").load("flStatus.php", "trip_id="+trip_id);
            var refreshId = setInterval(function() {
                $("#responsecontainer").load('flStatus.php', 'trip_id='+trip_id+'&did='+did);
            }, 1000);
            $.ajaxSetup({ cache: false});        
        });
    </script>

    
</script>    

</body>

</html>

<?php
if (isset ($conn)){
    mysqli_close($conn);
}
?>
