<?php
require_once('../header.php');
  
  if(isset($_GET["deleteFilter"])&&isset($_GET["id"])){
    $query="DELETE FROM ".$database_table_prefix."orderlist_filters WHERE id_orderlist_filter=".$_GET["id"];
    $db_conn->query($query);
  }
  if(isset($_GET["editFilter"])&&isset($_GET["id"])){
    if(!isset($_POST["id_status"]))
      $final_value="None";
    else
      $final_value=implode(",",$_POST["id_status"]);
    $query="UPDATE ".$database_table_prefix."orderlist_filters SET orderlist_filter_value='".$final_value."',orderlist_filter_name='".$_POST["orderlist_filter_name"]."' WHERE id_orderlist_filter=".$_GET["id"];
    //echo $query;
    $db_conn->query($query);
  }
  $query="SELECT * FROM ".$database_table_prefix."orderlist_filters";
  //echo $query;
  $result=$db_conn->query($query);
  if($result->num_rows>0){
    $filter_rows=array();
    while($row=$result->fetch_assoc())
      $filter_rows[]=$row;
  }
  $query="SELECT * FROM ".$database_table_prefix."statuses";
  //echo $query;
  $result=$db_conn->query($query);
  if($result->num_rows>0){
    $status_rows=array();
    while($row=$result->fetch_assoc())
      $status_rows[$row["id_status"]]=$row["status_name"];
  }
  $filter_headers=array("Name",/*"Filtered Fields",*/"Delete Filter");
  $filter_data=array("orderlist_filter_name",/*"orderlist_filter_value",*/"delete_filter");
?>
<html>
<head>
<title>Freeset Order Management</title>
<link rel="stylesheet" type="text/css" href="/css/standard.css">
</head>
<body>
<a href="/">[Back]</a>
<a href="/">[Home]</a>
<center>
<h1>Current Filter List</h1>
<button type="button" onclick="window.location='addFilter.php'">Add New Filter</button>
<br>
<table cellpadding="5">
<?php
  echo '<tr>';
  foreach($filter_headers as $header){
    echo '<th>'.$header."</th>";    
  }
  echo '</tr>';
  foreach($filter_rows as $filter_row){
    echo '<tr>';
      foreach($filter_data as $data){
        $output="";
        if($data=="orderlist_filter_value"){
          $status_ids=explode(",",$filter_row[$data]);
          $status_names=array();
          foreach($status_ids as $id){
            $status_names[]=$status_rows[$id];
          }
          if($filter_row[$data]=="None"){
            $output="None";
          }else{
            $output=implode(",",$status_names);
          }
          //$output=count($status_names);
        }elseif($data=="delete_filter"){
          $output='<button type="button" onclick="window.location=\'listFilters.php?deleteFilter&id='.$filter_row["id_orderlist_filter"].'\';">Delete Filter</button>';
        }elseif($data=="orderlist_filter_name"){
          $output='<a href="editFilter.php?id='.$filter_row["id_orderlist_filter"].'">'.$filter_row[$data].'</a>';
        }else{
          $output=$filter_row[$data];
        }
        echo '<td>'.$output.'</td>';
      }
    echo '</tr>';
  }
?>
</table>
</center>
</body>
</html>

<?php
require_once('../footer.php');
?>