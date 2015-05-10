<?php
require_once("../header.php");

$query="SELECT id_order FROM ".$database_table_prefix."orders WHERE order_number='".$_POST["order_number"]."'";
$result=$db_conn->query($query);
if($result->num_rows==1){
  $row=$result->fetch_assoc();
  $id_order=$row["id_order"];
  header("HTTP/1.1 303 See Other");
  header("Location: http://$_SERVER[HTTP_HOST]/orderDetails.php?id=$id_order");
}
else{
  header("HTTP/1.1 303 See Other");
  header("Location: http://$_SERVER[HTTP_HOST]/index.php?notFound");

}
require_once("../footer.php");
?>