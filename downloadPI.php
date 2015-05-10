<?php
require_once("header.php");
$query="SELECT order_number,pi_file_location FROM ".$database_table_prefix."orders WHERE id_order=".$_GET["id"];
$results=$db_conn->query($query);
if($results->num_rows==1){
  $row=$results->fetch_assoc();
  $file_location="../pi/".$row["pi_file_location"];
  //echo $file_location;
  if(file_exists($file_location)){
    header('Content-Type:application/vnd.openxmlformats-officedocument.spreadsheetml.sheet;');
    header('Content-Disposition: attachment; filename="pi_'.$row["order_number"].'.xlsx"');
    readfile($file_location);
  }else
    echo "file not exists";
}
require_once("footer.php");
?>                                                                                                                                                                                 