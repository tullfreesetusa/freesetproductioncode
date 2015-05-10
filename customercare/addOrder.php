<?php
require_once("../header.php");

  $query="SELECT * FROM ".$database_table_prefix."distributors";
  $result=$db_conn->query($query);
  if($result->num_rows>0)
    while($row=$result->fetch_assoc())
      $distributors_array[$row["id_distributor"]]=$row["distributor_name"];
?>
<html>
<head>
<title>
Add Product - Freeset Order Management
</title>
<link rel="stylesheet" type="text/css" href="/css/standard.css">
</head>
<body>
<a href="index.php">[Back]</a>
<center>
<h2>Add New Order</h2>
<form method="post" action="index.php?addOrder">
<table>
<tr>
<td>Order ID:</td><td><input type="text" name="order_number" id="order_number"></td></tr>
<tr><td>Distributor:</td><td><?php makeGenericOptionList(0,"id_distributor",$distributors_array,true)?></tr>
<tr><td align="center" colspan="2"><input type="submit"></td></tr>
</table>
</form>
</center>
</body>
<?php
require_once("../footer.php");
?>