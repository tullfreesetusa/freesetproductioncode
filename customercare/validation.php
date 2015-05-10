<?php
require_once('../header.php');
if(!isset($_GET["id"])){
  header("HTTP/1.1 303 See Other");
  header("Location: http://$_SERVER[HTTP_HOST]/customercare/index.php");
}
else{
  $is_valid=true;
  $error_array=array();
  $warning_array=array();
  $order_id=$_GET["id"];
  
  //Get order/product information and validate it's existance
  $query="SELECT *,TIMESTAMP(ship_date) AS clean_ship_date,TIMESTAMP(confirmation_date) AS clean_confirmation_date,TIMESTAMP(arrival_date) AS clean_arrival_date FROM ".$database_table_prefix."orders WHERE id_order=".$order_id;
  $results=$db_conn->query($query);
  if($results->num_rows!=1){
    $is_valid=false;
    $error_array[]="Order does not exist.";
  }    
  $query="SELECT *,".$database_table_prefix."products.id_product AS clean_id_product FROM ".$database_table_prefix."products LEFT JOIN ".$database_table_prefix."product_printing ON ".$database_table_prefix."product_printing.id_product=".$database_table_prefix."products.id_product LEFT JOIN ".$database_table_prefix."designations ON ".$database_table_prefix."designations.id_designation=".$database_table_prefix."products.id_designation WHERE product_active=1 AND id_order=".$order_id;
  //echo $query;
  $results2=$db_conn->query($query);
  if($results2->num_rows==0){
    $is_valid=false;
    $error_array[]="No products have been added to the order. Please add at least one product.";
  }else{
    $product_rows=array();
    while($row=$results2->fetch_assoc())
      $product_rows[]=$row;
  }
  $query="SELECT * FROM ".$database_table_prefix."order_addresses WHERE id_order=".$order_id;
  //echo $query;
  $results3=$db_conn->query($query);
  if($results3->num_rows!=2){
    $error_array[]="Billing and shipping addresses have not been added. Please add them to the order.";
    $is_valid=false;
  }
  else{
    while($row=$results3->fetch_assoc()){
      $address_rows[]=$row;
    }
  }
  
  if($is_valid==true){
    
    //Order validation
    
    $order_row=$results->fetch_assoc();
    if(!isset($order_row["payment_terms"])&&$order_row["payment_terms"]>0){
      $error_array[]="Payment Terms must be set.";
      $is_valid=false;
    }
    if(!isset($order_row["ship_date"])){
      $error_array[]="Ship Date must be set.";
      $is_valid=false;
    }
    else{
      $ship_date_stamp=strtotime($order_row["clean_ship_date"]);
      if(isPast($ship_date_stamp)){
        $error_array[]="Ship Date is in the past. Please correct it.";
        $is_valid=false;
      }
      elseif(isPast(strtotime("-7 days",$ship_date_stamp))){
        $warning_array[]="Ship Date is less than a week away. Please confirm that this is correct.";
        $is_valid=false;
      }
    }
    if(!isset($order_row["clean_arrival_date"])){
      /*$error_array[]="Arrival Date must be set";
      $is_valid=false;*/
    }
    else{
      $arrival_date_stamp=strtotime($order_row["clean_arrival_date"]);
      if(isPast($arrival_date_stamp)){
        $error_array[]="Arrival Date is in the past. Please correct it.";
        $is_valid=false;
      }
      elseif(isPast(strtotime("-14 days",$ship_date_stamp))){
        $warning_array[]="Arrival Date is less than two weeks away. Please confirm that this is correct.";
        $is_valid=false;
      }
    }
    if(!isset($order_row["id_shipping_method"])){
      $error_array[]="Shipping Method is not set. Please select a shipping method.";
      $is_valid=false;
    }
    if(!isset($order_row["withhold_invoice"])){
      $error_array[]="Invoice Withholding has not been set. Please set Yes or No.";
      $is_valid=false;
    }
    elseif($order_row["withhold_invoice"]==0){
      $warning_array[]="Invoice is not being withheld. Please confirm this is correct.";
      $is_valid=false;
    }
    if(!isset($order_row["id_currency"])){
      $error_array[]="Currency is not set. Please select a currency.";
      $is_valid=false;
    }
    if(!isset($order_row["buyer_shipping_num"])){
      $error_array[]="Buyer Shipping Number is not set. Please enter a buyer shipping number.";
      $is_valid=false;
    }
    if(!isset($order_row["id_certificate"])){
      $warning_array[]="Certificate of Origin is not set. Please confirm that this is correct.";
      $is_valid=false;
    }
    if(!isset($order_row["id_duty"])){
      $error_array[]="Duty Payer is not set. Please set it.";
      $is_valid=false;
    }
    
    //Product validation
    
    $product_ids=array();
    $product_numbers=array();
    foreach($product_rows as $product_row){
      $product_ids[]=$product_row["id_product"];
      $product_numbers[]=$order_row["order_number"].$product_row["designation_name"];
      $product_number=$order_row["order_number"].$product_row["designation_name"];
      if(!isset($product_row["design_description"])){
        $error_array[]=$product_number.": Design Description is not set. Please set a value.";
        $is_valid=false;
      }
      if(!isset($product_row["front_fabric"])||!isset($product_row["front_color"])){
        $error_array[]=$product_number.": Front Fabric must be set. Please set a fabric and color.";
        $is_valid=false;
      }
      if(!isset($product_row["gusset_fabric"])&&!isset($product_row["gusset_color"])){
        $warning_array[]=$product_number.": Gusset fabric is not set. Please confirm this is correct.";
        $is_valid=false;
      }elseif(isset($product_row["gusset_fabric"]) xor isset($product_row["gusset_fabric"])){
        $error_array[]=$product_number.": Gusset fabric and color are not both set. Please ensure both are valid.";
        $is_valid=false;
      }
      if(!isset($product_row["handle_field_1"])&&!isset($product_row["handle_field_2"])&&!isset($product_row["strap_details"])){
        $warning_array[]=$product_number.": No handle or strap information has been set. Please confirm this is correct.";
        $is_valid=false;
      }
      if(!isset($product_row["fastener_details"])){
        $warning_array[]=$product_number.": No fastener details have been set. Please confirm this is correct.";
      }
      if(!isset($product_row["tag_details"])){
        $error_array[]=$product_number.": No tag details have been set. Please set tag details.";
        $is_valid=false;
      }
      if(!isset($product_row["label_details"])){
        $error_array[]=$product_number.": No label details have been set. Please set label details.";
        $is_valid=false;
      }
      if($product_row["mockup_current"]==0&&isset($product_row["mockup_location"])){
        $error_array[]=$product_number.": The mockup for this product has not been confirmed. Please confirm the mockup.";
        $is_valid=false;
      }elseif(!isset($product_row["mockup_location"])){
        $warning_array[]=$product_number.": No mockup set for this product. Please confirm this is correct.";
        $is_valid=false;
      }
      
      //Print validation
      
      if(($product_row["front_screens"]==0||!isset($product_row["front_screens"])) xor ((!isset($product_row["front_pantones"]))||($product_row["front_screens"] != count(explode(",",$product_row["front_pantones"]))))){
        $error_array[]=$product_number.": Front screen and pantone numbers do not match. Please check pantone #s.";
        $is_valid=false;
      }
      if(($product_row["back_screens"]==0||!isset($product_row["back_screens"])) xor ((!isset($product_row["back_pantones"]))||($product_row["back_screens"] != count(explode(",",$product_row["back_pantones"]))))){
        $error_array[]=$product_number.": Back screen and pantone numbers do not match. Please check pantone #s.";
        $is_valid=false;
      }
      if(($product_row["left_screens"]==0||!isset($product_row["left_screens"])) xor ((!isset($product_row["left_pantones"]))||($product_row["left_screens"] != count(explode(",",$product_row["left_pantones"]))))){
        $error_array[]=$product_number.": Left screen and pantone numbers do not match. Please check pantone #s.";
        $is_valid=false;
      }
      if(($product_row["right_screens"]==0||!isset($product_row["right_screens"])) xor ((!isset($product_row["right_pantones"]))||($product_row["right_screens"] != count(explode(",",$product_row["right_pantones"]))))){
        $error_array[]=$product_number.": Right screen and pantone numbers do not match. Please check pantone #s.";
        $is_valid=false;
      }
      if(($product_row["other_screens"]==0||!isset($product_row["other_screens"])) xor ((!isset($product_row["other_pantones"]))||($product_row["other_screens"] != count(explode(",",$product_row["other_pantones"]))))){
        $error_array[]=$product_number.": Other screen and pantone numbers do not match. Please check pantone #s.";
        $is_valid=false;
      }
      if(!isset($product_row["order_quantity"])){
        $error_array[]=$product_number.": No quantity set for this product. Please set a quantity.";
        $is_valid=false;
      }elseif($product_row["order_quantity"]<100){
        $warning_array[]=$product_number.": Quantity for this product is less than 100 items. Please confirm this is correct.";
        $is_valid=false;
      }
      if(!isset($product_row["unit_price"])){
        $error_array[]=$product_number.": No price set for this product. Please set a price.";
        $is_valid=false;
      }
    } 
  }
  if($is_valid){
    header("HTTP/1.1 303 See Other");
    header("Location: http://$_SERVER[HTTP_HOST]/customercare/orderDetails.php?id=".$order_row["id_order"]."&validationSuccessful");
    exit;
  }
  $warnings_exist=false;
  $errors_exist=false;
  if(count($warning_array)>0)
    $warnings_exist=true;
  if(count($error_array)>0)
    $errors_exist=true;  
  echo $doctype;?>
  <html>
  <head>
  <title>Freeset Order Management</title>
  <link rel="stylesheet" type="text/css" href="/css/standard.css">
  <script>
  function checkWarningsAccepted(){
    var warningsAccepted=true;
    var warnings=document.getElementsByName("warning_accepted");
    for(i=0;i<warnings.length;i++){
      if (!warnings[i].checked){
        warningsAccepted=false;
      }
    }
    if(warningsAccepted)
      document.getElementById("validate_warnings").disabled=false;
    else
      document.getElementById("validate_warnings").disabled=true;
  }
  </script>
  </head>
  <body>
  <a href="orderDetails.php?id=<?php echo $order_id;?>">[Back]</a>
  <center>
  <?php if($errors_exist){ ?>
  <table class="error_table" bgcolor="#FFB0B0">
  <tr><th>Errors</th></tr>
  <?php
  foreach($error_array as $error)
    echo '<tr><td>'.$error.'</td></tr>';
  ?>
  </table>
  <br>
  <?php }
  if($warnings_exist){
  ?>
  <table type="warning_table" bgcolor="#FFFF99">
  <tr><th colspan="2">Warnings</th></tr>
  <?php
  foreach($warning_array as $warning)
    echo '<tr><td>'.$warning.'</td><td><input type="checkbox" name="warning_accepted" onclick="checkWarningsAccepted();"></td></tr>';
  ?>
  </table>
  <?php
  if(!$errors_exist){
    echo '<br><button type="button" id="validate_warnings" onclick="window.location=\'orderDetails.php?id='.$order_id.'&validationSuccessful\';" disabled>Ignore Warnings</button>';
  }}
  ?>
  <br><a href="editOrder.php?id=<?php echo $order_id;?>">Edit Order</a><br>
  <?php
  if(count($product_rows)>0)
    foreach($product_rows as $product_row){
      echo '<a href="editProduct.php?id='.$product_row["clean_id_product"].'">Edit Product: '.$order_row["order_number"].$product_row["designation_name"].' - '.$product_row["product_name"].'<br>';
  }
  ?>
  </center>
  </body>
  </html>
<?php
}
require_once('../footer.php');
?>