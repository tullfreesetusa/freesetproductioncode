<?php
require_once("header.php");
$id_address=$_GET["id"];
$query="SELECT * FROM ".$database_table_prefix."order_addresses WHERE id_address=".$id_address;
//echo $query;
$result=$db_conn->query($query);
if($result->num_rows==1){
  $row=$result->fetch_assoc();
}
$order_id=$row["id_order"];
?>
<html>
<head>
<title>
Edit Address - Freeset Order Management
</title>
<link rel="stylesheet" type="text/css" href="/css/standard.css">
</head>
<body>
<a href="orderDetails.php?id=<?php echo $order_id;?>" class="back_button">[Back]</a>
<a href="index.php">[Home]</a>
<center>
<h1>Edit <?php echo $row["type"]; ?> Address</h1>
<form action="orderDetails.php?id=<?php echo $order_id;?>&updateAddress" method="post" name="address_edit">
<table>
<tr><?php makeInputTextField("Name:","name",$row);?>
</tr><tr><?php makeInputTextField("Address:","address",$row);?>
</tr><tr><?php makeInputTextField("ZIP Code:","zip",$row);?>
</tr><tr><?php makeInputTextField("Country:","country",$row);?>
</tr><tr><?php makeInputTextField("Phone #:","phone",$row);?>
</tr><tr><?php makeInputTextField("Email:","email",$row);?></tr>
<tr><td><input type="text" style="visibility:hidden" name="id_address" id="id_address" value="<?php echo $id_address;?>">
<input type="text"style="visibility:hidden" name="type" id="type" value="<?php echo $row["type"];?>"</td></tr>
</table>
<input type="submit">
</form>
</center>
</body>
</html>
<?php
require_once("footer.php");
?>