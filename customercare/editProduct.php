<?php
require_once("../header.php");
$product_id=$_GET["id"];
$submitted=$_GET["submitted"];

  $query="SELECT * FROM ".$database_table_prefix."products LEFT JOIN ".$database_table_prefix."orders ON ".$database_table_prefix."products.id_order=".$database_table_prefix."orders.id_order LEFT JOIN ".$database_table_prefix."product_printing ON ".$database_table_prefix."products.id_product=".$database_table_prefix."product_printing.id_product LEFT JOIN ".$database_table_prefix."currencies ON ".$database_table_prefix."currencies.id_currency=".$database_table_prefix."orders.id_currency LEFT JOIN ".$database_table_prefix."designations ON ".$database_table_prefix."designations.id_designation=".$database_table_prefix."products.id_designation WHERE ".$database_table_prefix."products.id_product=".$product_id." AND product_active=1";
  $result=$db_conn->query($query);
  if($result->num_rows==1){
    $product_row=$result->fetch_assoc();
    $product_exists=true;
    $order_id=$product_row["id_order"];
    $product_number=$product_row["order_number"].$product_row["product_designation"];
    if(isset($product_row["id_print"]))
      $print_exists=true;
    if(isset($product_row["mockup_location"]))
      $mockup_exists=true;
  }
  $query="SELECT * FROM ".$database_table_prefix."fabrics ORDER BY fabric_name";
  $result2=$db_conn->query($query);
  $fabric_array=array();
  while($row=$result2->fetch_assoc()){
    $fabric_array[$row["id_fabric"]]=$row["fabric_name"];
  }
  $query="SELECT * FROM ".$database_table_prefix."colors ORDER BY color_name";
  $result3=$db_conn->query($query);
  $color_array=array();
  while($row=$result3->fetch_assoc()){
    $color_array[$row["id_color"]]=$row["color_name"];
  }
  $query="SELECT * FROM ".$database_table_prefix."accessories WHERE accessory_hidden=0";
  if(isset($product_row["accessories_details"])){
    $query=$query." OR id_accessory IN (".$product_row["accessories_details"].")";
  }
  $query=$query." ORDER BY accessory_name";
  //echo $query;
  $result4=$db_conn->query($query);
  $accessory_array=array();
  $accessory_hidden=array();
  while($row=$result4->fetch_assoc()){
    $accessory_array[$row["id_accessory"]]=$row["accessory_name"];
  }
?>
<html>
<head>
<script src="js/validation.js"></script>
<script>
function disableFabricSelectBox(option){
  if(document.getElementById(option+"_enabled").checked){
    document.getElementById(option+"_fabric").disabled=false;
    document.getElementById(option+"_color").disabled=false;
  }else{
    document.getElementById(option+"_fabric").disabled=true;
    document.getElementById(option+"_color").disabled=true;
  }
}
function disableOtherBox(){
  var details = document.getElementsByName("accessories_details[]");
  var otherBox = document.getElementsByName("accessories_other[]");
  for(i=0; i<details.length;i++){
    if(details[i].value=="-1"){
      otherBox[i].style.visibility="visible";
    }
    else{
      otherBox[i].style.visibility="hidden";
    }
  }
}
function disableAccessoriesSelectBox(){
  var details = document.getElementsByName("accessories_details[]");
  var enabled = document.getElementsByName("accessories_enabled[]");
  var counts = document.getElementsByName("accessories_counts[]");
  var otherBox = document.getElementsByName("accessories_other[]");
  for(i=0; i<details.length;i++){
    if(enabled[i].checked){
      details[i].disabled=false;
      otherBox[i].disabled=false;
      counts[i].disabled=false;  
    }
    else{
      otherBox[i].disabled=true;
      details[i].disabled=true;
      counts[i].disabled=true;
    }
  }
}
function disablePrintBoxes(){
  var status=!(document.getElementById("print_enabled").checked);
  //document.getElementById("print_name").disabled=status;
  document.getElementById("front_screens").disabled=status;
  document.getElementById("back_screens").disabled=status;
  document.getElementById("right_screens").disabled=status;
  document.getElementById("left_screens").disabled=status;
  document.getElementById("other_screens").disabled=status;
  document.getElementById("front_pantones").disabled=status;
  document.getElementById("back_pantones").disabled=status;
  document.getElementById("left_pantones").disabled=status;
  document.getElementById("right_pantones").disabled=status;
  document.getElementById("other_pantones").disabled=status;
}
function disableUploadBox(){
  if (document.getElementById("mockup_unchanged")){
    var status=!(document.getElementById("mockup_unchanged").checked);
    document.getElementById("mockup").disabled=status;
  }
}
</script>
<title>
<?php echo $product_number." - "; ?>Freeset Order Management
</title>
<link rel="stylesheet" type="text/css" href="/css/standard.css">
</head>
<body>
<a href="productDetails.php?id=<?php echo $product_id;?>" class="back_button">[Back]</a>
<a href="index.php">[Home]</a>
<center>
<h1>Product # <?php echo $product_number.$product_row["designation_name"];?></h1>
<h2><?php
  echo $product_row["product_name"];
  if(isset($product_row["design_description"]))                       
    echo " - ".$product_row["design_description"];                     
?></h2>
<h4><?php echo $product_row["reference_code"];?></h4>

<?php
  if(isset($product_row["mockup_location"])){
    echo '<img src="/img/'.$product_row["mockup_location"].'" height="200">';
  }
  if($product_row["mockup_current"]==0)
    echo '<h3 style="color:red">Mockup not Current. Please update or confirm.</h3>';                                                 
?>

<form name="product_edit" action="productDetails.php?id=<?php echo $product_id;?>&updateProduct" method="post" enctype="multipart/form-data" onsubmit="validate('product_edit')">
<table border="1" class="mockup_table">
<tr align="center"><td>Upload Mockup(JPG Only):</td><td><input type="file" name="mockup" id="mockup"></td>
<?php if(isset($product_row["mockup_location"])) echo '<td>New Mockup Needed?:<input type="checkbox" name="mockup_unchanged" id="mockup_unchanged" onclick="disableUploadBox();" checked></td>';?>
</tr>
</table>
<table class="product_summary_table" cellpadding="10">
<tr><tr><td>Dimensions (h x w x d):</td><td><input type="text" name="height" id="height" style="width:25px" maxlength="3" value=<?php echo $product_row["height"];?>> x <input type="text" name="width" id="width" style="width:25px" maxlength="3" value=<?php echo $product_row["width"];?>> x <input type="text" name="depth" id="depth" style="width:25px" maxlength="3" value=<?php echo $product_row["depth"];?>></td><?php makeInputTextField("Design Description:  ".$product_row["product_name"]." - ","design_description",$product_row);?>
</tr>
<tr><?php makeInputTextField("H.S. Code:","hs_code",$product_row);?>
<?php makeInputTextField("Customer Reference:","customer_reference",$product_row);?></tr>
<td>Unit Price: <?php echo $product_row["currency_symbol"];?></td><td><input type="text" name="unit_price" id="unit_price" size="4" value="<?php echo $product_row["unit_price"];?>"></td><td>Quantity:</td><td><input type="text" name="order_quantity" id="order_quantity" size="4" value="<?php echo $product_row["order_quantity"];?>"></td></tr>
<!--<tr><?php makeInputTextField("Product Name:  ".$product_row["product_name"]." - ","design_description",$product_row);?>
<?php makeInputTextField("Product Reference:","reference_code",$product_row);?></tr>
<tr><?php makeInputTextField("H.S. Code:","hs_code",$product_row);?>
<?php makeInputTextField("Customer Reference:","customer_reference",$product_row);?></tr>
<tr><td>Dimensions (h x w x d):</td><td><input type="text" name="height" id="height" style="width:25px" maxlength="3" value="<?php echo $product_row["height"];?>"> x <input type="text" name="width" id="width" style="width:25px" maxlength="3" value="<?php echo $product_row["width"];?>"> x <input type="text" name="depth" id="depth" style="width:25px" maxlength="3" value="<?php echo $product_row["depth"];?>"></td>
<td>Unit Price: <?php echo $product_row["currency_symbol"];?><input type="text" name="unit_price" id="unit_price" size="4" value="<?php echo $product_row["unit_price"];?>"></td><td>Quantity:<input type="text" name="order_quantity" id="order_quantity" size="4" value="<?php echo $product_row["order_quantity"];?>"></td></tr>
--></table>
<hr>
<table class="fabric_details" cellpadding="5">
<tr><td>Front Fabric:</td>
<td><?php makeFullOptionList("front",$product_row,$color_array,$fabric_array);?></td>
</tr>
<tr><td>Gusset Fabric:</td>
<td><?php makeFullOptionList("gusset",$product_row,$color_array,$fabric_array);?></td>
</tr>
<tr><td>Flap Fabric:</td>
<td><?php makeFullOptionList("flap",$product_row,$color_array,$fabric_array);?></td>
</tr>
<!--<tr><td>Handle Fabric:</td>
<td><?php makeFullOptionList("handle",$product_row,$color_array,$fabric_array);?></td>
</tr>-->
<tr><td>Lining Fabric:</td>
<td><?php makeFullOptionList("lining",$product_row,$color_array,$fabric_array);?></td>
</tr>
</table>
<hr>
<table class="accessories_table" cellpadding="5">
<?php
  $product_accessories=array();
  if(isset($product_row["accessories_details"]))
    $product_accessories=explode(',',$product_row["accessories_details"]);
    $product_accessories_counts=explode(',',$product_row["accessories_counts"]);?>                             
<tr><td>Accessory 1:</td><td><?php makeAccessoryOptionList($product_accessories[0],$product_accessories_counts[0],$accessory_array);?></td></tr>
<tr><td>Accessory 2:</td><td><?php makeAccessoryOptionList($product_accessories[1],$product_accessories_counts[1],$accessory_array);?></td></tr>
<tr><td>Accessory 3:</td><td><?php makeAccessoryOptionList($product_accessories[2],$product_accessories_counts[2],$accessory_array);?></td></tr>
<tr><td>Accessory 4:</td><td><?php makeAccessoryOptionList($product_accessories[3],$product_accessories_counts[3],$accessory_array);?></td></tr>
<tr><td>Accessory 5:</td><td><?php makeAccessoryOptionList($product_accessories[4],$product_accessories_counts[4],$accessory_array);?></td></tr>
</table>
<hr>
<table class="other_details" cellpadding="5">
<tr><?php makeInputTextField("Fastener Details:","fastener_details",$product_row);?></tr>
<tr><?php makeInputTextField("Tag Details:","tag_details",$product_row);?></tr>
<tr><?php makeInputTextField("Label Details:","label_details",$product_row);?></tr>
<tr><?php makeInputTextField("Strap Details:","strap_details",$product_row);?></tr>
<tr><td>Handle Type:</td><td><input type="text" name="handle_field_1" id="handle_field_1" value="<?php if (isset($product_row["handle_field_1"])) echo $product_row["handle_field_1"]; else echo "";?>"></td></tr><tr><td>Handle Details:</td><td><input type="text" name="handle_field_2" id="handle_field_2" value="<?php if (isset($product_row["handle_field_2"])) echo $product_row["handle_field_2"]; else echo "";?>"></td></tr>
<tr><td>Extra Details:</td><td><textarea rows="5" cols="46" name="extras_details" id="extras_details"><?php echo $product_row["extras_details"];?></textarea></td></tr>
</table>
<input type="text" style="visibility:hidden" name="id_print" id="id_print" value="<?php echo $product_row["id_print"];?>">
<hr>
<table class="printing_table">
<tr><td>Has Print?</td><td><input type="checkbox" name="print_enabled" id="print_enabled" onclick="disablePrintBoxes();" <?php if(isset($product_row["id_print"])) echo "checked";?>></td></tr>
<!--<tr><td>Print Name:</td><td><input size="40" type="text" name="print_name" id="print_name" value="<?php echo $product_row["print_name"];?>" <?php if(!isset($product_row["id_print"])) echo "disabled";?>></td></tr>-->
<tr>
<td colspan="2"><table width="250">
<tr><th>Location</th><th>Screens</th><th>Pantones (Comma Separated)</th></tr>
<tr><td>Front</td><?php makeScreenInputField("front_screens",$product_row);makeGenericTextField("front_pantones",$product_row,55,!isset($product_row["id_print"]));?></tr>
<tr><td>Back</td><?php makeScreenInputField("back_screens",$product_row);makeGenericTextField("back_pantones",$product_row,55,!isset($product_row["id_print"]));?></tr>
<tr><td>Left</td><?php makeScreenInputField("left_screens",$product_row);makeGenericTextField("left_pantones",$product_row,55,!isset($product_row["id_print"]));?></tr>
<tr><td>Right</td><?php makeScreenInputField("right_screens",$product_row);makeGenericTextField("right_pantones",$product_row,55,!isset($product_row["id_print"]));?></tr>
<tr><td>Other</td><?php makeScreenInputField("other_screens",$product_row);makeGenericTextField("other_pantones",$product_row,55,!isset($product_row["id_print"]));?></tr>
</tr>
</table></td></tr>
</table>
<br>
<input type="submit">
</form>
</center>
</body>
</html>
<?php
require_once("../footer.php");
?>