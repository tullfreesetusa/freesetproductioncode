<?php
require_once("../header.php");
  
  $order_id=$_GET["id"];
  $query="SELECT id_order, designation FROM ".$database_table_prefix."order LEFT JOIN ".$database_table_prefix."products ON ".$database_table_prefix."order.id_order=".$database_table_prefix."products.id_order LEFT JOIN ".$database_table_prefix."designations ON ".$database_table_prefix."designations.id_designation=".$database_table_prefix."products.id_designation WHERE ".$database_table_prefix."order.id_order=".$order_id;
  /*$result=$db_conn->query($query);
  while($row=$result->fetch_assoc()){
    $product_designations=array();
    $product_designations[]=$row["product_designation"];
  }*/
  echo $query."\n";
  $query="SELECT id_order product";
  echo $query."\n";
?>
<html>
<head>
<title>
Add Product - Freeset Order Management
</title>
<link rel="stylesheet" type="text/css" href="/css/standard.css">
</head>
<body>
<center>
<h1>Add Product to Order</h1>
<form>

</form>
</center>
</body>
</html>
<?php
require_once("../footer.php");
?>