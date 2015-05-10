<?php
require_once("../header.php");
  
  $filter_id=$_GET["id"];
  $query="SELECT * FROM ".$database_table_prefix."orderlist_filters WHERE id_orderlist_filter=".$filter_id;
  //echo $query;
  $result=$db_conn->query($query);
  if($result->num_rows==1){
    $filter_row=$result->fetch_assoc();
  }
  $query="SELECT * FROM ".$database_table_prefix."statuses ORDER BY status_name";
  $result=$db_conn->query($query);
  if($result->num_rows>0){
    $status_rows=array();
    while($row=$result->fetch_assoc())
      $status_rows[]=$row;
  }
?>
<html>
<head>
<title>Freeset Order Management</title>
<link rel="stylesheet" type="text/css" href="/css/standard.css">
</head>
<body>
<a href="listFilters.php">[Back]</a>
<a href="index.php">[Home]</a>
<center>
<h1>Edit Filter</h1>
<form method="post" name="filter_edit" action="listFilters.php?editFilter&id=<?php echo $filter_row["id_orderlist_filter"]?>">
Name: <input type="text" name="orderlist_filter_name" id="orderlist_filter_name" value="<?php echo $filter_row["orderlist_filter_name"];?>">
<h2>Select Statuses to Hide</h2>
<table cellpadding="5">
<?php
  echo '<tr>';
  $filtered_statuses=explode(",",$filter_row["orderlist_filter_value"]);
  $i=0;
  foreach($status_rows as $status_row){
    if($i>8){
      echo '</tr><tr>';
      $i=0;
    }
    echo '<td><input type="checkbox" name="id_status[]" value="'.$status_row["id_status"].'"';
    if((in_array($status_row["id_status"],$filtered_statuses)))
      echo " checked";
    echo '></td><td>'.$status_row["status_name"].'</td>';
    $i++;
  }
  echo '</tr>';
?>
</table>
<input type="submit" value="Save">
</center>
</body>
</html>
<?php
require_once("../footer.php");
?>                                                                               