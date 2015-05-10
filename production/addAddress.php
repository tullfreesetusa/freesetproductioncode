<?php
require_once("header.php");
$address_type=$_GET["type"];
$order_id=$_GET["id"];

$query="INSERT INTO ".$database_table_prefix."order_addresses (id_order,type) VALUES ($order_id,'$address_type')";
$db_conn->query($query);
$id_address=$db_conn->insert_id;
header("HTTP/1.1 303 See Other");
if($user=="customercare"){
  header("Location: http://$_SERVER[HTTP_HOST]/customercare/editAddress.php?id=$id_address");
}elseif($user=="production"){
  header("Location: http://$_SERVER[HTTP_HOST]/production/editAddress.php?id=$id_address");
}
require_once("footer.php");
?>