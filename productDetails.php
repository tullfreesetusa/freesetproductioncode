<?php
require_once("header.php");
$product_id=$_GET["id"];
if (isset($_GET["updateProduct"])){
  if(file_exists($_FILES["mockup"]["tmp_name"]) || is_uploaded_file($_FILES["mockup"]["tmp_name"])){
    $target_dir="../img/";
    $upload_ok=true;
    
    do{
      $target_name="mockup_".mt_rand(100000,999999).".jpg";
      $target_file=$target_dir.$target_name;
    }while(file_exists($target_file));
    $filetype=$_FILES["mockup"]["type"];
    if(/*!$filetype=="image/png"||*/!$filetype=="image/jpeg"){
      $upload_ok=false;
    }
    if ($_FILES["mockup"]["size"] > 500000){
      //echo $_FILES["mockup"]["size"];
      $upload_ok=false;
    }
    if($upload_ok){
      if (move_uploaded_file($_FILES["mockup"]["tmp_name"],$target_file)){
        $mockup_file_location=$target_name;
      }
    } 
  }
  $simple_updates=array(/*"product_name","reference_code",*/"design_description","customer_reference","hs_code","height","width","depth","order_quantity","unit_price","fastener_details","label_details","extras_details","handle_field_1","handle_field_2","strap_details","tag_details");
  $production_updates=array("front","gusset","flap","lining");
  $query="UPDATE ".$database_table_prefix."products SET ";
  foreach($simple_updates as $sql_field){
    if(strlen(trim($_POST[$sql_field]))==0)
      $query=$query.$sql_field."=NULL,";
    elseif(is_numeric(trim($_POST[$sql_field])))
      $query=$query.$sql_field."=".trim($_POST[$sql_field]).",";
    else
      $query=$query.$sql_field."='".trim($_POST[$sql_field])."',";
  }
  foreach($production_updates as $sql_field){
    if(isset($_POST[$sql_field."_enabled"])){
      $query=$query.$sql_field."_color=".$_POST[$sql_field."_color"].",";
      $query=$query.$sql_field."_fabric=".$_POST[$sql_field."_fabric"].",";
    }
    else{
      $query=$query.$sql_field."_color=NULL,";
      $query=$query.$sql_field."_fabric=NULL,";
    }
  }
  if(isset($_POST["accessories_details"])){
    for($i=0;$i<count($_POST["accessories_details"]);$i++){
      if($_POST["accessories_details"][$i]=="-1"){
        $sub_query="INSERT INTO ".$database_table_prefix."accessories (accessory_name,accessory_reference,accessory_hidden) VALUES ('".$_POST["accessories_other"][$i]."','CUSTOM',1)";
        //echo $sub_query;
        $db_conn->query($sub_query);
        $_POST["accessories_details"][$i]=$db_conn->insert_id;
      }
    }
    $accessories_details=$_POST["accessories_details"];
    $accessories_counts=$_POST["accessories_counts"];
    $accessories_details_string="";
    $accessories_count_string="";
    for($i=0;$i<count($accessories_details);$i++){
      if(isset($accessories_details[$i])&&isset($_POST["accessories_enabled"][$i])&&($accessories_details[$i]!=0)){
        $accessories_details_string=$accessories_details_string.$accessories_details[$i].',';
        if(isset($accessories_counts[$i])){
          $accessories_counts_string=$accessories_counts_string.$accessories_counts[$i].',';
        }else{
          $accessories_counts_string=$accessories_counts_string.'1,';
        }
      }
    }
    $accessories_details_string=rtrim($accessories_details_string,',');
    $accessories_counts_string=rtrim($accessories_counts_string,',');
    $query=$query."accessories_details='$accessories_details_string',accessories_counts='$accessories_counts_string'";
  }else{
    $query=$query."accessories_details=NULL";
    $query=$query.",accessories_counts=NULL";
  }
  $query=rtrim($query,",");
  if(isset($mockup_file_location))
    $query=$query.",mockup_location='".$mockup_file_location."',mockup_current=1";
  elseif(!isset($_POST["mockup_unchanged"])){
    $query=$query.",mockup_current=1";
  }
  else
    $query=$query.",mockup_current=0";
  $query=rtrim($query,",");

  $query=$query." WHERE id_product=$product_id;";
  //$query=mysqli_escape_string($db_conn, $query);
  //echo $query;
  if(isset($_POST["design_description"])){
    $db_conn->query($query);
    /*if($db_conn->affected_rows==1)
      echo '<center><font color="green">Successful Update</font></center>';
    else
      echo '<center><font color="red">Update Failed</font></center>';*/
  }
    else
      echo '<center><font color="red">Update Failed</font></center>';
  $printing_updates=array("print_name","front_screens","back_screens","left_screens","right_screens","other_screens","front_pantones","back_pantones","left_pantones","right_pantones","other_pantones");
  $printing_inserts=array("id_product","print_name","front_screens","back_screens","right_screens","left_screens","other_screens","front_pantones","back_pantones","left_pantones","right_pantones","other_pantones");
  if((isset($_POST["id_print"])&&!$_POST["id_print"]=="")&&isset($_POST["print_enabled"])){
    $query="UPDATE ".$database_table_prefix."product_printing SET ";
    foreach($printing_updates as $sql_field){
      if(strlen(trim($_POST[$sql_field]))==0)
        $query=$query.$sql_field."=NULL,";
      elseif(is_numeric(trim($_POST[$sql_field])))
        $query=$query.$sql_field."=".trim($_POST[$sql_field]).",";
      else
        $query=$query.$sql_field."='".trim($_POST[$sql_field])."',";
    }
    $query=rtrim($query,',');
    $query=$query." WHERE id_print=".$_POST["id_print"];
  }
  elseif((isset($_POST["id_print"])&&!$_POST["id_print"]=="")&&!isset($_POST["print_enabled"])){
    $query="DELETE FROM ".$database_table_prefix."product_printing WHERE id_print=".$_POST["id_print"].";";
  }
  elseif(!(isset($_POST["id_print"])&&!$_POST["id_print"]=="")&&isset($_POST["print_enabled"])){
    $query="INSERT INTO ".$database_table_prefix."product_printing (";
    foreach($printing_inserts as $inserts){
      $query=$query.$inserts.",";
    }
    $query=rtrim($query,',');
    $query=$query.") VALUES (";
    foreach($printing_inserts as $sql_field){
      if($sql_field=="id_product")
        $query=$query.$product_id.",";
      elseif(strlen(trim($_POST[$sql_field]))==0)
        $query=$query."NULL,";
      elseif($_POST[$sql_field]==0&&is_numeric($_POST[$sql_field]))
        $query=$query."NULL,";
      elseif(is_numeric(trim($_POST[$sql_field])))
        $query=$query.trim($_POST[$sql_field]).",";
      else
        $query=$query."'".trim($_POST[$sql_field])."',";
    }
    $query=rtrim($query,',');
    $query=$query.");";
  }
  if(!(!(isset($_POST["id_print"])&&!$_POST["id_print"]=="")&&!isset($_POST["print_enabled"]))){
    //echo $query;
    $db_conn->query($query);
  }
  $query="SELECT id_order FROM ".$database_table_prefix."products WHERE id_product=".$product_id;
  $order_row=$db_conn->query($query)->fetch_assoc();
  $query="UPDATE ".$database_table_prefix."orders SET order_validated=0 WHERE id_order=".$order_row["id_order"];
  //echo $query;
  $db_conn->query($query);
}  
  $product_summary_headers=array(/*"Product Name","Code",*/"Customer Ref.","Dimensions (h x w x d)","H.S. Code","Quantity","Unit Price","Total Price");
  $product_summary_data=array(/*"product_name","reference_code",*/"customer_reference","dimensions","hs_code","order_quantity","unit_price","total_price");
  $production_details_headers=array("Front Fabric","Gusset Fabric","Flap Fabric","Lining Fabric");
  $production_details_data=array("front","gusset","flap","lining");
  $production_details_headers_2=array("Handle","Strap","Fastener","Tag","Label");
  $production_details_data_2=array("handle","strap_details","fastener_details","tag_details","label_details");
  $print_details_headers=array("Location","Screens");
  $print_details_locations=array("front","back","left","right","other");
  $print_details_data=array("screens","pantones");
  
  $product_exists=false;
  $print_exists=false;
  $mockup_exists=false;
  $query="SELECT * FROM ".$database_table_prefix."products LEFT JOIN ".$database_table_prefix."orders ON ".$database_table_prefix."products.id_order=".$database_table_prefix."orders.id_order LEFT JOIN ".$database_table_prefix."product_printing on ".$database_table_prefix."products.id_product=".$database_table_prefix."product_printing.id_product LEFT JOIN ".$database_table_prefix."currencies ON ".$database_table_prefix."currencies.id_currency=".$database_table_prefix."orders.id_currency LEFT JOIN ".$database_table_prefix."designations ON ".$database_table_prefix."designations.id_designation=".$database_table_prefix."products.id_designation WHERE ".$database_table_prefix."products.id_product=".$product_id." AND product_active=1";
  //echo $query;
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
  //echo $query;
  $query="SELECT * FROM ".$database_table_prefix."fabrics";
  $result2=$db_conn->query($query);
  $fabric_array=array();
  while($row=$result2->fetch_assoc()){
    $fabric_array[$row["id_fabric"]]=$row["fabric_name"];
  }
  $query="SELECT * FROM ".$database_table_prefix."colors";
  $result3=$db_conn->query($query);
  $color_array=array();
  while($row=$result3->fetch_assoc()){
    $color_array[$row["id_color"]]=$row["color_name"];
  }
  $query="SELECT * FROM ".$database_table_prefix."accessories ORDER BY accessory_reference";
  $result4=$db_conn->query($query);
  $accessory_array=array();
  while($row=$result4->fetch_assoc()){
    $accessory_array[$row["id_accessory"]]=$row["accessory_name"].';'.$row["accessory_reference"];
  }
?>
<html>
<head>
<title>
<?php echo $product_number." - "; ?>Freeset Order Management
</title>
<link rel="stylesheet" type="text/css" href="/css/standard.css">
</head>
<body>
<a href="orderDetails.php?id=<?php echo $order_id;?>" class="back_button">[Back]</a>
<a href="/">[Home]</a>
<?php
  if ($product_exists){
?>
<center>
<h1>Product # <?php echo $product_number.$product_row["designation_name"];?></h1>
<h2><?php
  echo $product_row["product_name"];
  if(isset($product_row["design_description"]))
    echo " - ".$product_row["design_description"];                     
?></h2>
<h4><?php echo $product_row["reference_code"];?></h4>
<?php if ($mockup_exists){ ?>
<img src="/img/<?php echo $product_row["mockup_location"];?>" height ="200">
<?php 
  if($product_row["mockup_current"]==0)
    echo '<h3 style="color:red">Mockup Not Current</h3>';
  else
    echo '<br>';
  }
?>
<?php if(!$product_row["order_locked"]==1&&$user=="customercare"){?><button type="button" onclick="window.location='editProduct.php?id=<?php echo $product_id;?>';">Edit Product</button><?php } ?>
<h2>Product Overview</h2>
<table class="product_overview" border="1">
<tr>
<?php
  foreach($product_summary_headers as $header){
    echo "<th class=\"".$th_class."\">".$header."</th>";
  }
?>
</tr>
<tr>
<?php
  foreach($product_summary_data as $data){
    if($data=="dimensions"){
      if(isset($product_row["depth"]))
        $output=$product_row["height"]." x ".$product_row["width"]." x ".$product_row["depth"];
      else
        $output=$product_row["height"]." x ".$product_row["width"];
    }
    elseif($data=="total_price"){
      $output=$product_row["currency_symbol"].$product_row["unit_price"]*$product_row["order_quantity"];
    }
    elseif($data=="unit_price"){
      $output=$product_row["currency_symbol"].$product_row["unit_price"];
    }
    elseif(!isset($product_row[$data])){
      $output="None";
    }
    else
      $output=$product_row[$data];  
    echo "<td class=\"".$td_class."\">".$output."</td>";
  }
?>
</tr>
</table>
<hr>
<h2>Production Details</h2>
<table class="production_details" border="1">
<tr>
<?php
  foreach($production_details_headers as $header){
    echo "<th class=\"".$th_class."\">".$header."</th>";
  }
?>
</tr>
<tr>
<?php
  foreach($production_details_data as $data){
    $output="";
    if($data=="front"||$data=="gusset"||$data=="flap"||$data=="lining"){
      if((!isset($product_row[$data."_color"]))&&(!isset($product_row[$data."_fabric"])))
        $output="None";
      else{
        $output=$color_array[$product_row[$data."_color"]]." ".$fabric_array[$product_row[$data."_fabric"]];
      }
    }
    elseif($data=="handle"){
      if(isset($product_row["handle_field_1"])||isset($product_row["handle_field_2"]))
        $output=$product_row["handle_field_1"]." ".$product_row["handle_field_2"];
      else
        $output="None";
    }                                                     
    elseif(!isset($product_row[$data])||$product_row[$data]==""){
      $output="None";
    }
    else
      $output=$product_row[$data];  
    echo "<td class=\"".$td_class."\">".$output."</td>";
  }
?>
</tr>
</table>
<br>
<table class="production_details" border="1">
<tr>
<?php
  foreach($production_details_headers_2 as $header){
    echo "<th class=\"".$th_class."\">".$header."</th>";
  }
?>
</tr>
<tr>
<?php
  foreach($production_details_data_2 as $data){
    $output="";
    if($data=="front"||$data=="gusset"||$data=="flap"||$data=="lining"){
      if((!isset($product_row[$data."_color"]))&&(!isset($product_row[$data."_fabric"])))
        $output="None";
      else{
        $output=$color_array[$product_row[$data."_color"]]." ".$fabric_array[$product_row[$data."_fabric"]];
      }
    }
    elseif($data=="handle"){
      if(isset($product_row["handle_field_1"])||isset($product_row["handle_field_2"]))
        $output=$product_row["handle_field_1"]." ".$product_row["handle_field_2"];
      else
        $output="None";
    }                                                     
    elseif(!isset($product_row[$data])||$product_row[$data]==""){
      $output="None";
    }
    else
      $output=$product_row[$data];  
    echo "<td class=\"".$td_class."\">".$output."</td>";
  }
?>
</tr>
</table>
<?php if(isset($product_row["accessories_details"])){?>
<h3>Accessories</h3>
<table border="1" cellpadding="5">
<tr><th>Reference</th><th>Accessory</th><th>Count</th></tr>
<?php $accessories=explode(',',$product_row["accessories_details"]);
  //echo $accessories_counts;
  $accessories_counts=explode(',',$product_row["accessories_counts"]);
  for($i=0;$i<count($accessories);$i++){
    $accessory_info=explode(';',$accessory_array[$accessories[$i]]);
    echo '<tr><td>'.$accessory_info[1].'</td><td>'.$accessory_info[0].'</td><td>'.$accessories_counts[$i].'</td></tr>';
  }
?>
</table>
<?php }?>
<?php if ($print_exists){?>
<hr>
<h2>Print Details</h2>
<table class="printing_details" border="1">
<tr>
<?php
  $max_pantones=0;
  foreach($print_details_locations as $locations){
    if(count(explode(",",$product_row[$locations."_pantones"]))>$max_pantones)
      $max_pantones=count(explode(",",$product_row[$locations."_pantones"]));
  }
  for($i=0;$i<$max_pantones;$i++){
    $print_details_headers[]="Pantone ".($i+1);
  }
  foreach($print_details_headers as $header){
    echo "<th class=\"".$th_class."\">".$header."</th>";
  }
?>
</tr>
<?php
  foreach($print_details_locations as $location){
    echo '<tr><td>'.ucfirst($location).'</td>';
    foreach($print_details_data as $data){
      if($data=="screens")  
        echo '<td>'.$product_row[$location."_".$data].'</td>';
      elseif($max_pantones==0)
        echo '<td>None</td>';
      else{
        $pantones=explode(",",$product_row[$location."_".$data]);
        for($i=0;$i<$max_pantones;$i++){
          if(isset($pantones[$i])&&isset($product_row[$location."_".$data])){
            echo '<td>'.$pantones[$i].'</td>';
          }
          else
            echo '<td>None</td>';
        }
      }
    }
    echo "</tr>";  
  }
?>
</table>
<?php if(isset($product_row["extras_details"])){?>
<hr>
<h2>Extra Comments</h2>
<table width="50%">
<tr>
<td><p><?php echo $product_row["extras_details"];?></td>
</tr>
</table>
<?php }?>
<?php if(!$product_row["order_locked"]==1&&$user=="customercare"){?><br><button type="button" onclick="window.location='editProduct.php?id=<?php echo $product_id;?>';">Edit Product</button><?php } ?>
<?php } ?>
<?php }
  else
    echo "Product does not exist. :("; ?>
</center>    
</body>
</html>
<?php
require_once("footer.php");
?>