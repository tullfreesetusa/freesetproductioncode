<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd"> 
<html>
<head>
<title>Tull and Beverly</title>
<link rel="stylesheet" type="text/css" href="style.css">
</head>
<body>
<center><h1>Tull Gearreald & Beverly Cope's Wedding</h1></center> 
<div class="nav_container">
<ul class="nav_bar">
<?php
  $nav_bar_array=array("About Us"=>"about","Wedding Info"=>"schedule","Travel Info"=>"location","Registry"=>"registry","Guest Book"=>"guestbook","R.S.V.P"=>"rsvp");
  foreach($nav_bar_array as $name=>$location){
    echo '<li class="nav_item"><a class="nav_link" href="#'.$location.'">'.$name.'</a></li>';
  }
?>
</ul>
</div>

<?php
  foreach($nav_bar_array as $name=>$location){
    echo "<hr>";
    include($location.".php");
  }
?>
</body>
</html>