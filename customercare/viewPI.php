<?php
require_once("../header.php");
  $order_id=$_GET["id"];
  $query="SELECT *,CONCAT('B',order_number) AS clean_order_number,DATE_FORMAT(ship_date,'%d %b. %Y') AS clean_ship_date,DATE_FORMAT(confirmation_date,'%d %b. %Y') AS clean_confirmation_date,DATE_FORMAT(arrival_date,'%d %b. %Y') AS clean_arrival_date FROM ".$database_table_prefix."orders  LEFT JOIN ".$database_table_prefix."statuses ON ".$database_table_prefix."statuses.id_status=".$database_table_prefix."orders.id_status LEFT JOIN ".$database_table_prefix."currencies ON ".$database_table_prefix."currencies.id_currency=".$database_table_prefix."orders.id_currency LEFT JOIN ".$database_table_prefix."shipping_terms ON ".$database_table_prefix."shipping_terms.id_shipping_terms=".$database_table_prefix."orders.id_shipping_terms LEFT JOIN ".$database_table_prefix."certificates ON ".$database_table_prefix."certificates.id_certificate=".$database_table_prefix."orders.id_certificate LEFT JOIN ".$database_table_prefix."duties ON ".$database_table_prefix."duties.id_duty=".$database_table_prefix."orders.id_duty LEFT JOIN ".$database_table_prefix."shipping_methods ON ".$database_table_prefix."shipping_methods.id_shipping_method=".$database_table_prefix."orders.id_shipping_method LEFT JOIN ".$database_table_prefix."distributors ON ".$database_table_prefix."distributors.id_distributor=".$database_table_prefix."orders.id_distributor WHERE id_order=".$order_id;
  //echo $query;
  $result=$db_conn->query($query);
  if($result->num_rows==1){
    $order_row=$result->fetch_assoc();
  }
  $order_headers=array("Freeset Order No.","Customer Reference","","Payment Terms","Currency","Ship Date","Delivery Date","Shipping Method","Withhold Invoice","FOB/CIF","Shipping Account #","Certificate of Origin","Duty Charged to");
  $order_data=array("clean_order_number","customer_reference","","payment_terms","currency_name","clean_ship_date","clean_arrival_date","shipping_method_name","withhold_invoice","shipping_terms_name","buyer_shipping_num","certificate_name","duty_name");
  
  $query="SELECT *,CONCAT('".$order_row["clean_order_number"]."',designation_name) AS 'clean_product_number',unit_price*order_quantity AS order_total FROM ".$database_table_prefix."products LEFT JOIN ".$database_table_prefix."product_printing ON ".$database_table_prefix."product_printing.id_product=".$database_table_prefix."products.id_product  LEFT JOIN ".$database_table_prefix."designations ON ".$database_table_prefix."designations.id_designation=".$database_table_prefix."products.id_designation WHERE id_order=".$order_id." AND product_active=1";
  //echo $query;
  $result2=$db_conn->query($query);
  $products_data=array();
  if($result2->num_rows>0){
    while($row=$result2->fetch_assoc()){
      $products_data[]=$row;
    }
  }
  $query="SELECT * FROM ".$database_table_prefix."accessories";
  $result3=$db_conn->query($query);
  if($result3->num_rows>0){
    while($row=$result3->fetch_assoc()){
      $accessories_array[$row["id_accessory"]]=$row["accessory_name"];
    }
  }
  $query="SELECT * FROM ".$database_table_prefix."fabrics";
  $result4=$db_conn->query($query);
  if($result4->num_rows>0){
    while($row=$result4->fetch_assoc()){
      $fabrics_array[$row["id_fabric"]]=$row["fabric_name"];
    }
  }
  $query="SELECT * FROM ".$database_table_prefix."colors";
  $result5=$db_conn->query($query);
  if($result5->num_rows>0){
    while($row=$result5->fetch_assoc()){
      $colors_array[$row["id_color"]]=$row["color_name"];
    }
  }
  $product_headers=array("Product Name","Product Code","Order ID","Customer Ref Code","H.S. Code","Dimensions","Front Fabric","Gusset Fabric","Handles & Straps","Flap Fabric","Lining Fabric","Accessories","Fasteners","Label and Tag","Print Name","Front","Back","Left","Right","Other","Extras","Quantity","Unit Price","Total");
  $product_data=array("product_name","reference_code","designation_name","customer_reference","hs_code","dimensions","front","gusset","handles","flap","lining","accessories","fastener_details","label","design_description","front_screens","back_screens","left_screens","right_screens","other_screens","extras_details","order_quantity","unit_price","order_total");
  ?>

<html>
<head>
<title>Freeset PI</title>
<link rel="stylesheet" type="text/css" href="../css/standard.css">
</head>
<body>
<a href="orderDetails.php?id=<?php echo $order_row["id_order"];?>">[Back]</a>
<a href="/">[Home]</a>
<center>
<h1>PI Info</h1>
<table border="1" cellpadding="15">
<tr><?php
  foreach($order_headers as $header){
    echo '<th>'.$header.'</th>';
  }
?></tr>
<tr>
<?php
  foreach($order_data as $data){
    $output="";
    if($data=="withhold_invoice"){
      if($order_row[$data]==1)
        $output="Yes";
      else
        $output="No";
    }
    else{
      $output=$order_row[$data];
    }  
    echo '<td>'.$output.'</td>';
      
  }
?>
</tr>
</table>
<h3>Products</h3>
<table border="1" cellpadding="15">
<tr>
<?php
  foreach($product_headers as $header){
    echo '<th>'.$header.'</th>';
  }
?>
</tr>
<?php
  foreach($products_data as $product_row){
    echo "<tr>";
    foreach($product_data as $data){
      $output="";
      if($data=="handles"){
        $output=$product_row["handle_field_1"]."/".$product_row["handle_field_2"]."/".$product_row["strap_details"];
      }elseif($data=='label'){
        $output=$product_row["label_details"]."/".$product_row["tag_details"];
      }elseif($data=='accessories'){
        $accessory_array=explode(",",$product_row["accessories_details"]);
        foreach($accessory_array as $accessories_id){
          $output=$output.$accessories_array[$accessories_id]."/";
        }
        $output=rtrim($output,"/");
      }elseif($data=="dimensions"){
        $output=$product_row["height"].' x '.$product_row["width"];
        if(isset($product_row["depth"]))
          $output=$output.' x '.$product_row["depth"];
      }elseif($data=="front"||$data=="flap"||$data=="lining"||$data=="gusset"){
        $output=$colors_array[$product_row[$data."_color"]]." ".$fabrics_array[$product_row[$data."_fabric"]];
      }elseif($data=="unit_price"||$data=="order_total"){
        $output=$order_row["currency_symbol"].$product_row[$data];
      }
      else
        $output=$product_row[$data];
      if(strlen(trim($output))==0){
        $output="None";
      }
      echo '<td>'.$output.'</td>';
    }
    echo "</tr>";
  }
?>
</table>
<h2>Product Mockups</h2>
<?php
  foreach($products_data as $product_row){
    echo '<h4>'."B".$order_row["order_number"].$product_row["designation_name"].'</h4>';
    echo '<img src="img/'.$product_row["mockup_location"].'">';
  }

?>
</center>
</body>
</html>
<?php
require_once("../footer.php");
?>