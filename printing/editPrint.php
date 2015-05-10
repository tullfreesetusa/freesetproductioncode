<?php
require_once("../header.php");
$product_id=$_GET["id"];
$submitted=$_GET["submitted"];

  $query="SELECT * FROM ".$database_table_prefix."products LEFT JOIN ".$database_table_prefix."orders ON ".$database_table_prefix."products.id_order=".$database_table_prefix."orders.id_order LEFT JOIN ".$database_table_prefix."product_printing ON ".$database_table_prefix."products.id_product=".$database_table_prefix."product_printing.id_product LEFT JOIN ".$database_table_prefix."designations ON ".$database_table_prefix."designations.id_designation=".$database_table_prefix."products.id_designation WHERE ".$database_table_prefix."products.id_product=".$product_id." AND product_active=1";
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
  $query="SELECT * FROM ".$database_table_prefix."accessories ORDER BY accessory_reference";
  $result4=$db_conn->query($query);
  $accessory_array=array();
  while($row=$result4->fetch_assoc()){
    $accessory_array[$row["id_accessory"]]=$row["accessory_name"];
  }
?>
<html>
<head>
<script src="js/validation.js"></script>
<title>
<?php echo $product_number." - "; ?>Freeset Order Management
</title>
<script>
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
<link rel="stylesheet" type="text/css" href="/css/standard.css">
</head>
<body>
<a href="index.php" class="back_button">[Back]</a>
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

<form name="print_edit" action="index.php?print_id=<?php echo $product_row["id_print"];?>&product_id=<?php echo $product_id;?>&updatePrint" method="post" enctype="multipart/form-data" onsubmit="validate('product_edit')">
<input type="hidden" name="id_print" value="<?php echo $product_row["id_print"]?>">
<table border="1" class="mockup_table">
<tr align="center"><td>Upload Mockup(JPG Only):</td><td><input type="file" name="mockup" id="mockup"></td>
<?php if(isset($product_row["mockup_location"])) echo '<td>New Mockup Needed?:<input type="checkbox" name="mockup_unchanged" id="mockup_unchanged" onclick="disableUploadBox();" checked></td>';?>
</tr>
</table>
<hr>
<table class="printing_table">
<tr><td>Print Name:</td><td><input size="40" type="text" name="design_description" id="design_description" value="<?php echo $product_row["design_description"];?>" <?php if(!isset($product_row["id_print"])) echo "disabled";?>></td></tr>
<tr>
<td colspan="2"><table width="250">
<tr><td>Has Print?</td><td><input type="checkbox" name="print_enabled" id="print_enabled" onclick="disablePrintBoxes();" <?php if(isset($product_row["id_print"])) echo "checked";?>></td></tr>
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