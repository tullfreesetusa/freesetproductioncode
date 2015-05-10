<?php
require_once("../header.php");
  
  $order_id=$_GET["id"];
  $query="SELECT *,DATE_FORMAT(ship_date,'%d-%m-%y') AS clean_ship_date,DATE_FORMAT(confirmation_date,'%d-%m-%y') AS clean_confirmation_date,DATE_FORMAT(arrival_date,'%d-%m-%y') AS clean_arrival_date FROM ".$database_table_prefix."orders  LEFT JOIN ".$database_table_prefix."statuses ON ".$database_table_prefix."statuses.id_status=".$database_table_prefix."orders.id_status LEFT JOIN ".$database_table_prefix."distributors ON ".$database_table_prefix."distributors.id_distributor=".$database_table_prefix."orders.id_distributor WHERE id_order=".$order_id;
  $result=$db_conn->query($query);
  if($result->num_rows==1){
    $order_row=$result->fetch_assoc();
    $order_exists=true;
  }
  $query="SELECT * FROM ".$database_table_prefix."currencies ORDER BY id_currency";
  $result2=$db_conn->query($query);
  $currency_array=array();
  if($result2->num_rows>0){
    while($row=$result2->fetch_assoc())
      $currency_array[$row["id_currency"]]=$row["currency_name"];
  }
  $query="SELECT * FROM ".$database_table_prefix."shipping_methods ORDER BY id_shipping_method";
  $result3=$db_conn->query($query);
  $shipping_method_array=array();
  if($result3->num_rows>0){
    while($row=$result3->fetch_assoc())
      $shipping_method_array[$row["id_shipping_method"]]=$row["shipping_method_name"];
  }
  $query="SELECT * FROM ".$database_table_prefix."shipping_terms ORDER BY id_shipping_terms";
  $result4=$db_conn->query($query);
  $shipping_terms_array=array();
  if($result4->num_rows>0){
    while($row=$result4->fetch_assoc())
      $shipping_terms_array[$row["id_shipping_terms"]]=$row["shipping_terms_name"];
  }
  $query="SELECT * FROM ".$database_table_prefix."statuses";
  $result5=$db_conn->query($query);
  $status_array=array();
  if($result5->num_rows>0){
    while($row=$result5->fetch_assoc())
      $status_array[$row["id_status"]]=$row["status_name"];
  }
  $query="SELECT * FROM ".$database_table_prefix."certificates";
  $result6=$db_conn->query($query);
  $certificate_array=array();
  if($result6->num_rows>0){
    while($row=$result6->fetch_assoc())
      $certificate_array[$row["id_certificate"]]=$row["certificate_name"];
  }
  $query="SELECT * FROM ".$database_table_prefix."duties";
  $result7=$db_conn->query($query);
  $duty_array=array();
  if($result7->num_rows>0){
    while($row=$result7->fetch_assoc())
      $duty_array[$row["id_duty"]]=$row["duty_name"];
  }
?>
<html>
<head>
<title>
<?php if($order_exists) echo $order_row["order_number"]." - ";?>Freeset Order Management
</title>
<link rel="stylesheet" type="text/css" href="/css/standard.css">
</head>
<body>
<a href="orderDetails.php?id=<?php echo $order_id;?>" class="back_button">[Back]</a>
<a href="index.php">[Home]</a>
<center>
<h1>Order # <?php echo $order_row["order_number"];?></h1>
<h2><?php
  echo $order_row["distributor_name"];
  if(isset($order_row["customer_details"])){
    echo " - ".$order_row["customer_details"];
  }
?></h2>
<form action="orderDetails.php?id=<?php echo $order_id;?>&updateOrder" method="post" name="order_edit">
<!--Status: <?php makeGenericOptionList($order_row["id_status"],"id_status",$status_array,false);?>-->
<table>
<tr>
<?php makeInputTextField("Customer: ".$order_row["distributor_name"]." - ","customer_details",$order_row);?>
</tr><tr><?php makeInputTextField("Payment Terms:","payment_terms",$order_row);?>
</tr><tr><?php makeInputTextField("Ship Date (DD-MM-YY):","clean_ship_date",$order_row);?>
</tr><tr><?php makeInputTextField("Dispatch Notes:","dispatch_notes",$order_row);?>
</tr><tr><td align="center">Flexible?</td><td><input type="radio" name="flexible_ship_date" value="1"<?php if($order_row["flexible_ship_date"]==1) echo " checked"; ?>>Yes<input type="radio" name="flexible_ship_date"<?php if(isset($order_row["flexible_ship_date"])&&$order_row["flexible_ship_date"]==0) echo " checked";?>>No</td>
</tr><tr><?php makeInputTextField("Confirmation Date (DD-MM-YY):","clean_confirmation_date",$order_row);?>
</tr><tr><?php makeInputTextField("Delivery Date (DD-MM-YY):","clean_arrival_date",$order_row);?>
</tr><tr><td>Shipping Method:</td><td><?php makeGenericOptionList($order_row["id_shipping_method"],"id_shipping_method",$shipping_method_array,true);?></td>
</tr><tr><td>Shipping Terms:</td><td><?php makeGenericOptionList($order_row["id_shipping_terms"],"id_shipping_terms",$shipping_terms_array,true);?></td>
</tr><tr><td>Withhold Invoice?</td><td><input type="radio" name="withhold_invoice" value="1"<?php if(!isset($order_row["withhold_invoice"])||$order_row["withhold_invoice"]==1) echo " checked"; ?>>Yes<input type="radio" name="withhold_invoice" value="0"<?php if(isset($order_row["withhold_invoice"])&&$order_row["withhold_invoice"]==0) echo " checked";?>>No</td>
</tr><tr><td>Currency</td><td><?php makeGenericOptionList($order_row["id_currency"],"id_currency",$currency_array,true);?></td>
</tr><tr><?php makeInputTextField("Buyer Shipping #:","buyer_shipping_num",$order_row);?>
</tr><tr><td>GSP/Certificate of Origin:</td><td><?php makeGenericOptionList($order_row["id_certificate"],"id_certificate",$certificate_array,false);?></td>
</tr><tr><td>Duties Charged To:</td><td><?php makeGenericOptionList($order_row["id_duty"],"id_duty",$duty_array,false);?></td>
</tr>
</table>
<input type="submit">
</form>
</center>
</body>
</html>
<?php
require_once("../footer.php");
?>