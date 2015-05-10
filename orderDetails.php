<?php
require_once("header.php");
  $order_id=$_GET["id"];
  if (isset($_GET["unlockOrder"])){
    $query="UPDATE ".$database_table_prefix."orders SET order_validated=0,order_locked=0 WHERE id_order=".$order_id;
    //echo $query;
    $db_conn->query($query);
  }
  if (isset($_GET["uploadPI"])){
    if(file_exists($_FILES["pi_upload"]["tmp_name"]) || is_uploaded_file($_FILES["pi_upload"]["tmp_name"])){
      $target_dir="../pi/";
      $upload_ok=true;
      
      do{
        $target_name="pi_".mt_rand(100000,999999).".xlsx";
        $target_file=$target_dir.$target_name;
      }while(file_exists($target_file));
      $filetype=$_FILES["pi_upload"]["type"];
      if(!$filetype=="application/vnd.openxmlformats-officedocument.spreadsheetml.sheet"){
        $upload_ok=false;
      }
      if ($_FILES["pi_upload"]["size"] > 2*1024*1024){
        //echo $_FILES["mockup"]["size"];
        $upload_ok=false;
      }
      if($upload_ok){
        if (move_uploaded_file($_FILES["pi_upload"]["tmp_name"],$target_file)){
          $pi_file_location=$target_name;
        }
      } 
    }
    if(isset($pi_file_location)){
      $query="UPDATE ".$database_table_prefix."orders SET order_locked=1,id_status=36,pi_file_location='".$pi_file_location."' WHERE id_order=".$order_id;
      //echo $query;
      $db_conn->query($query);
    }
  }

  if(isset($_GET["validationSuccessful"])){
    $query="UPDATE ".$database_table_prefix."orders SET order_validated=1 WHERE id_order=".$order_id;
    $db_conn->query($query);
  }
  if(isset($_GET["deactivateProduct"])&&isset($_GET["product_id"])){
    echo "DEACTIVATION!!";
    $query="UPDATE ".$database_table_prefix."products SET product_active=0 WHERE id_product=".$_GET["product_id"];
    //echo $query;
    $db_conn->query($query);
  }
  if(isset($_GET["updateAddress"])&&isset($_POST["id_address"])){
    $address_updates=array("name"=>$_POST["name"],"address"=>$_POST["address"],"zip"=>$_POST["zip"],"country"=>$_POST["country"],"phone"=>$_POST["phone"],"email"=>$_POST["email"]);
    $query="UPDATE ".$database_table_prefix."order_addresses SET ";
    foreach($address_updates as $field=>$value){
      if(strlen(trim($value))==0){
        $query=$query.$field."=NULL,";
      }/*elseif(is_numeric(trim($value))){
        $query=$query.$field."=".trim($value).",";
      }*/else{
        $query=$query.$field."='".trim($value)."',";
      }
    }
    $query=rtrim($query,",");
    $query=$query." WHERE id_address=".$_POST["id_address"].";";
    //echo $query;
    $db_conn->query($query);
  }
  if(isset($_GET["addProduct"])&&isset($_POST["id_catalog"])){
    $query="SELECT * FROM ".$database_table_prefix."catalog WHERE id_catalog=".$_POST["id_catalog"];
    //echo $query;
    $results=$db_conn->query($query);
    if($results->num_rows==1){
      $catalog_row=$results->fetch_assoc();      
      $product_inserts=array("id_designation"=>$_POST["id_designation"],"product_name"=>$catalog_row["product_name"],"reference_code"=>$catalog_row["reference_code"],"design_description"=>$catalog_row["design_description"],"height"=>$catalog_row["height"],"width"=>$catalog_row["width"],"depth"=>$catalog_row["depth"],"handle_field_1"=>$catalog_row["handle_field_1"],"tag_details"=>$catalog_row["tag_details"],"label_details"=>$catalog_row["label_details"],"sewing_timing"=>$catalog_row["sewing_timing"],"accessories_timing"=>$catalog_row["accessories_timing"],"finishing_timing"=>$catalog_row["finishing_timing"],"id_order"=>$order_id);
      $field_string="";
      $value_string="";
      foreach ($product_inserts as $field=>$value){
        $field_string=$field_string.$field.",";
        if(strlen(trim($value))==0){
          $value_string=$value_string."NULL,";
        }elseif(is_numeric(trim($value))){
          $value_string=$value_string.trim($value).",";
        }else{
          $value_string=$value_string."'".trim($value)."',";
        }
      }
    }
    $query="INSERT INTO ".$database_table_prefix."products (".rtrim($field_string,",").") VALUES (".rtrim($value_string,",").");";
    //echo $query;
    $db_conn->query($query);
    $query="UPDATE ".$database_table_prefix."orders SET order_validated=0,last_update_date=CURDATE() WHERE id_order=".$order_id;
    $db_conn->query($query);
  }
  if(isset($_GET["updateOrder"])){
    $order_updates=array("customer_details"=>$_POST["customer_details"],"payment_terms"=>$_POST["payment_terms"],"dispatch_notes"=>$_POST["dispatch_notes"],"ship_date"=>"STR_TO_DATE('".$_POST["clean_ship_date"]."','%d-%m-%y')","arrival_date"=>"STR_TO_DATE('".$_POST["clean_arrival_date"]."','%d-%m-%y')","confirmation_date"=>"STR_TO_DATE('".$_POST["clean_confirmation_date"]."','%d-%m-%y')","id_shipping_method"=>$_POST["id_shipping_method"],"withhold_invoice"=>$_POST["withhold_invoice"],"id_shipping_terms"=>$_POST["id_shipping_terms"],"id_currency"=>$_POST["id_currency"],"buyer_shipping_num"=>$_POST["buyer_shipping_num"],"id_certificate"=>$_POST["id_certificate"],"id_duty"=>$_POST["id_duty"],/*"id_status"=>$_POST["id_status"],*/"flexible_ship_date"=>$_POST["flexible_ship_date"]);
    $query="UPDATE ".$database_table_prefix."orders SET ";
    foreach($order_updates as $field=>$value){
      if(strlen(trim($value))==0){
        $query=$query.$field."=NULL,";
      }elseif($field=="ship_date"||$field=="confirmation_date"||$field=="arrival_date"){
        //echo $value;
        if($value=="STR_TO_DATE('00-00-00','%d-%m-%y')"||$value=="STR_TO_DATE('','%d-%m-%y')")
          $query=$query.$field."=NULL,";
        else
          $query=$query.$field."=".trim($value).",";
      }elseif(is_numeric(trim($value))){
        $query=$query.$field."=".trim($value).",";
      }else{
        $query=$query.$field."='".trim($value)."',";
      }
    }
    $query=rtrim($query,",");
    $query=$query.",order_validated=0,last_update_date=CURDATE()";
    $query=$query." WHERE id_order=".$order_id.";";
    //echo $query;
    $db_conn->query($query);
  }
  $order_details_headers=array("Payment Terms","Confirmation Date","Ship Date","Dispatch Notes","Flexible?","Delivery Date","Shipping Method","Shipping Terms","With-hold Invoice<br>(Yes / No)","Currency","Shipping Number","GSP/Certificate of Origin","Duty To Be Charged To");
  $order_details_data=array("payment_terms","clean_confirmation_date","clean_ship_date","dispatch_notes","flexible_ship_date","clean_arrival_date","shipping_method_name","shipping_terms_name","withhold_invoice","currency_name","buyer_shipping_num","certificate_name","duty_name");
  if($user!="customercare"){
    $product_summary_headers=array("Mockup","Designation","Name","Code","Printed","Quanity","Price","Total");
    $product_summary_data=array("mockup_location","designation_name","product_name","reference_code","printed","order_quantity","unit_price","total_price");
  }
  else{
    $product_summary_headers=array("Mockup","Designation","Name","Code","Printed","Quanity","Price","Total","Delete Product");
    $product_summary_data=array("mockup_location","designation_name","product_name","reference_code","printed","order_quantity","unit_price","total_price","delete_product");
  }
  $order_exists=false;
  $products_exist=false;
  $shipping_exists=false;
  $billing_exists=false;
  $td_class="";
  $th_class="";
  $query="SELECT *,DATE_FORMAT(ship_date,'%d %b. %Y') AS clean_ship_date,DATE_FORMAT(confirmation_date,'%d %b. %Y') AS clean_confirmation_date,DATE_FORMAT(arrival_date,'%d %b. %Y') AS clean_arrival_date FROM ".$database_table_prefix."orders  LEFT JOIN ".$database_table_prefix."statuses ON ".$database_table_prefix."statuses.id_status=".$database_table_prefix."orders.id_status LEFT JOIN ".$database_table_prefix."currencies ON ".$database_table_prefix."currencies.id_currency=".$database_table_prefix."orders.id_currency LEFT JOIN ".$database_table_prefix."shipping_terms ON ".$database_table_prefix."shipping_terms.id_shipping_terms=".$database_table_prefix."orders.id_shipping_terms LEFT JOIN ".$database_table_prefix."certificates ON ".$database_table_prefix."certificates.id_certificate=".$database_table_prefix."orders.id_certificate LEFT JOIN ".$database_table_prefix."duties ON ".$database_table_prefix."duties.id_duty=".$database_table_prefix."orders.id_duty LEFT JOIN ".$database_table_prefix."shipping_methods ON ".$database_table_prefix."shipping_methods.id_shipping_method=".$database_table_prefix."orders.id_shipping_method LEFT JOIN ".$database_table_prefix."distributors ON ".$database_table_prefix."distributors.id_distributor=".$database_table_prefix."orders.id_distributor WHERE id_order=".$order_id;
  //echo $query;
  $result=$db_conn->query($query);
  if($result->num_rows==1){
    $order_row=$result->fetch_assoc();
    $order_exists=true;
  }
  $query="SELECT *,".$database_table_prefix."products.id_product AS 'clean_id' FROM ".$database_table_prefix."products LEFT JOIN ".$database_table_prefix."product_printing ON ".$database_table_prefix."product_printing.id_product=".$database_table_prefix."products.id_product  LEFT JOIN ".$database_table_prefix."designations ON ".$database_table_prefix."designations.id_designation=".$database_table_prefix."products.id_designation WHERE id_order=".$order_id." AND product_active=1";
  //echo $query;
  $result2=$db_conn->query($query);
  $products_data=array();
  if($result2->num_rows>0){
    $products_exist=true;
    while($row=$result2->fetch_assoc()){
      $products_data[]=$row;
    }
  }
  $query="SELECT * FROM ".$database_table_prefix."order_addresses WHERE id_order=".$order_id." AND type='Shipping'";
  $result3=$db_conn->query($query);
  if($result3->num_rows==1){
    $shipping_exists=true;
    $row=$result3->fetch_assoc();
    $shipping_id=$row["id_address"];
    $shipping_name=$row["name"];
    $shipping_address=$row["address"];
    $shipping_country=$row["country"];
    $shipping_zip=$row["zip"];
    $shipping_phone=$row["phone"];
    $shipping_email=$row["email"];
  }
  $query="SELECT * FROM ".$database_table_prefix."order_addresses WHERE id_order=".$order_id." AND type='Billing'";
  //echo $query;
  $result4=$db_conn->query($query);
  if($result4->num_rows==1){
    $billing_exists=true;
    $row=$result4->fetch_assoc();
    $billing_id=$row["id_address"];
    $billing_name=$row["name"];
    $billing_address=$row["address"];
    $billing_country=$row["country"];
    $billing_zip=$row["zip"];
    $billing_phone=$row["phone"];
    $billing_email=$row["email"];
  }
?>
<html>
<head>
<title>
<?php if($order_exists) echo $order_row["order_number"]." - "; ?>Freeset Order Details
</title>
<link rel="stylesheet" type="text/css" href="../css/standard.css">
</head>
<body>
<a href="index.php" class="back_button">[Back]</a>
<?php
  if ($order_exists){
?>
<center>
<h1>Order # <?php echo $order_row["order_number"]; ?></h1>
<!--Start Order Details Table-->
<h2>
<?php
  echo $order_row["distributor_name"];
  if(isset($order_row["customer_details"]))
    echo " - ".$order_row["customer_details"];
?>
</h2>
<h2>Status:</h2>
<?php if($order_row["order_validated"]==1&&$order_row["order_locked"]==0){ ?><h2 style="color:#00CC00;">Validated</h2><?php } ?>
<h2 style="color:<?php if($order_row["status_color"]!="FFFFFF")echo "#".$order_row["status_color"]; else echo "blue"; ?>"><?php echo $order_row["status_name"]; ?></h2>
<?php if(!$order_row["order_locked"]==1){ ?><?php if($user!="customercare"){echo "<!--";} ?><button type="button" onclick="window.location='editOrder.php?id=<?php echo $order_id; ?>';">Edit Order</button><br><br><?php if($user!="customercare"){echo "-->";} ?><?php } ?>
<?php if($order_row["order_validated"]==0&&$order_row["order_locked"]==0){ ?><?php if($user!="customercare"){echo "<!--";} ?><button type="button" onclick="window.location='validation.php?id=<?php echo $order_id; ?>';">Validate Order</button><?php if($user!="customercare"){echo "-->";} ?>
<?php }elseif($order_row["order_validated"]==1&&$order_row["order_locked"]==0){ ?><?php if($user!="customercare"){echo "<!--";} ?><button type="button" onclick="window.location='viewPI.php?id=<?php echo $order_id; ?>';">Generate PI</button><?php if($user!="customercare"){echo "-->";} ?>
<?php if($user!="customercare"){echo "<!--";} ?><div style="margin:15px"><form action="orderDetails.php?id=<?php echo $order_id; ?>&uploadPI" method="post" enctype="multipart/form-data">Upload Confirmed PI:<input type="file" name="pi_upload" id="pi_upload"><input type="submit" value="Submit Approved PI"></form></div><?php if($user!="customercare"){echo "-->";} ?>
<?php }else{ ?>   
<button type="button" onclick="window.location='downloadPI.php?id=<?php echo $order_row["id_order"]; ?>';">Download Approved PI</button><br><br>
<?php if($user!="customercare"){echo "<!--";} ?><button type="button" onclick="if(confirm('Are you sure you want to revise this order?')){window.location='orderDetails.php?id=<?php echo $order_row["id_order"]; ?>&unlockOrder';}">Revise Order</button><?php if($user!="customercare"){echo "-->";} ?>
<?php } ?>
<h2>Details</h2>
<!--Start Order Summary Table-->
<table class="order-summary" border="1">
<tr>
<?php
  foreach($order_details_headers as $header){
    echo "<th class=\"".$th_class."\">".$header."</th>";
  }
?>
</tr>
<tr>
<?php
  foreach($order_details_data as $data){
    if($data=="withhold_invoice"||$data=="flexible_ship_date"){
      if($order_row[$data]==0)
        $output="No";
      else
        $output="Yes";
    }
    else
      $output=$order_row[$data];  
    echo "<td class=\"".$td_class."\">".$output."</td>";
  }
?>
</tr>
</table>
<!--End Order Details Table-->
<hr>
<!--Start Product Summary Table-->
<h2>Product Summary</h2>
<?php if ($products_exist){ ?>
<table class="product_summary_table" border="1" cellpadding="10">
<tr>
<?php
  foreach($product_summary_headers as $header){
    if($header=="Delete Product"&&$order_row["order_locked"]==1)
      continue;
    echo "<th class=\"".$th_class."\">".$header."</th>";
  }
?>
</tr>
<?php
  foreach($products_data as $product_data){
    echo "<tr>";
    foreach($product_summary_data as $data){
      if ($data=="designation_name"){
        $output="<a href=\"productDetails.php?id=".$product_data["clean_id"]."\">".$order_row["order_number"].$product_data[$data]."</a>";
      }
      elseif($data=="mockup_location"){
        if(isset($product_data[$data]))
          $output='<img src="../img/'.$product_data[$data].'" width="50">';
        else
          $output='Not Set';
      }
      elseif ($data=="unit_price"){
        $output=$order_row["currency_symbol"].$product_data[$data];
      }
      elseif ($data=="total_price"){
        $output=$order_row["currency_symbol"].$product_data["unit_price"]*$product_data["order_quantity"];
      }
      elseif ($data=="printed"){
        if(isset($product_data["id_print"]))
          $output="Yes";
        else
          $output="No";
      }
      elseif($data=="delete_product"){
        if($order_row["order_locked"]==1)
          continue;
        $output='<button type="button" onclick="if(confirm(\'Are you sure you want to delete this product?\')){window.location=\'orderDetails.php?id='.$order_id.'&deactivateProduct&product_id='.$product_data["clean_id"].'\';}">Delete Product</button>';
      }
      elseif($data=="product_name"){
        $output=$product_data[$data]." - ".$product_data["design_description"];
      }
      else
        $output=$product_data[$data];
      echo "<td class=\"".$td_class."\">".$output."</td>";
    }
  }
?>
</tr>
</table>
<?php
  }
  else
    echo '<h3 style="color:red">No products currently associated with this order.<h3>';  
?>
<br><?php if(!$order_row["order_locked"]==1&&$user=="customercare"){ ?><button type="button" onclick="window.location='addProduct.php?id=<?php echo $order_id; ?>';">Add Product</button>
<?php } ?>
<!--End Product Summary Table-->

<hr>
<h2>Contact Details</h2>
<!--Start Address Container Table-->
<table class="address_details" border="1">
<tr valign="top">  

<td>
<table class="billing_details" cellpadding="15" border="0">
<tr>
<th colspan="2">Billing Details</th>
</tr>
<?php if($billing_exists){ ?>
<tr><td colspan="2" align="center"><button type="button" onclick="window.location='editAddress.php?id=<?php echo $billing_id;?>';">Edit Billing Details</button></td></tr>
<tr>
<td>Name</td>
<?php
  echo "<td class=\"".$td_class."\">".$billing_name."</td>";
?>
</tr>
<tr>
<td>Address</td>
<?php
  echo "<td class=\"".$td_class."\">".$billing_address."</td>";
?>
</tr>
<td>ZIP Code</td>
<?php
  echo "<td class=\"".$td_class."\">".$billing_zip."</td>";
?>
</tr>
<tr>
<td>Country</td>
<?php
  echo "<td class=\"".$td_class."\">".$billing_country."</td>";
?>
</tr>
<?php if (isset($billing_phone)){?>
<tr>
<td>Phone #</td>
<?php
  echo "<td class=\"".$td_class."\">".$billing_phone."</td>";
?>
</tr>
<?php } ?>
<?php if (isset($billing_email)){?>
<tr>
<td>Email</td>
<?php
  echo "<td class=\"".$td_class."\">".$billing_email."</td>";
?>
</tr>
<?php } ?>
<?php } else{ ?>
<tr><td colspan="2" align="center"><button type="button" onclick="window.location='addAddress.php?type=Billing&id=<?php echo $order_id;?>';">Add Billing Details</button></td></tr>
<?php } ?>
</table>
</td>
<td>
<table class="shipping_details" cellpadding="15" border="0">
<tr>
<th colspan="2">Shipping Details</th>
</tr>
<?php if($shipping_exists){?>
<tr><td colspan="2" align="center"><button type="button" onclick="window.location='editAddress.php?id=<?php echo $shipping_id;?>';">Edit Shipping Details</button></td></tr>
<tr>
<td>Name</td>
<?php
  echo "<td class=\"".$td_class."\">".$shipping_name."</td>";
?>
</tr>
<tr>
<td>Address</td>
<?php
  echo "<td class=\"".$td_class."\">".$shipping_address."</td>";
?>
</tr>
<tr>
<td>ZIP Code</td>
<?php
  echo "<td class=\"".$td_class."\">".$shipping_zip."</td>";
?>
</tr>
<tr>
<td>Country</td>
<?php
  echo "<td class=\"".$td_class."\">".$shipping_country."</td>";
?>
</tr>
<?php if (isset($shipping_phone)){?>
<tr>
<td>Phone #</td>
<?php
  echo "<td class=\"".$td_class."\">".$shipping_phone."</td>";
?>
</tr>
<?php } ?>
<?php if (isset($shipping_email)){ ?>
<tr>
<td>Email</td>
<?php
  echo "<td class=\"".$td_class."\">".$shipping_email."</td>";
?>
</tr>
<?php } ?>
<?php } else{ ?>
<tr><td colspan="2" align="center"><button type="button" onclick="window.location='addAddress.php?type=Shipping&id=<?php echo $order_id;?>';">Add Shipping Details</button></td></tr>
<?php } ?>
</table>
</td>
</tr>
</table>
</center>
<?php
}
  else{
    echo "Error! :(";
  }
?>
</body>
</html>
<?php
require_once("footer.php");
?>