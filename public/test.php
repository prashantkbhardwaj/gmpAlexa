<?php
  if (isset($_POST['submit'])) {
    $phrase = $_POST['phrase'];
    $pxp = str_split($phrase);
    $plen = strlen($phrase);
    $cn = 0;
    for ($i=0; $i < $plen; $i++) { 
      if ($pxp[$i]==$pxp[$i+1]) {
        $cn += 1;
        $pr .= "";
      } else {
        $pr .= $cn.$pxp[$i];
        $cn = 0;
      }
    }
    echo $pr;
  }
  if (isset($_POST['dsub'])) {
    $decrpt = $_POST['decrpt'];
    $dxd = str_split($decrpt);
    $plen = strlen($decrpt);
    for ($i=0; $i < $plen; $i+=2) { 
      for ($j=0; $j < $dxd[$i]+1; $j++) { 
        $prd .= $dxd[$i+1];
      }
    }
    echo $prd;
  } 
?>
<!DOCTYPE html>
<html>
<head>
  <title></title>
</head>
<body>
<form method="post" action="test.php">
  <input type="text" name="phrase" placeholder="encrypt">
  <input type="submit" name="submit" value="submit">
</form>
<form method="post" action="test.php">
  <input type="text" name="decrpt" placeholder="decrypt">
  <input type="submit" name="dsub" value="decrpt">
</form>
</body>
</html>