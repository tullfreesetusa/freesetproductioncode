<?php
require_once("../header.php");
$query="INSERT INTO ".$database_table_prefix."orderlist_filters (orderlist_filter_name,orderlist_filter_value) VALUES ('New Filter','None')";
//echo $query;
$db_conn->query($query);
$id_filter=$db_conn->insert_id;
header("HTTP/1.1 303 See Other");
header("Location: http://$_SERVER[HTTP_HOST]customercare/editFilter.php?id=$id_filter");
require_once("../footer.php");
?>