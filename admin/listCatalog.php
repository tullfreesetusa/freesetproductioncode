<?php
require_once("../header.php");
?>
<html>
<head>
<title>
Freeset Order Management
</title>
<link rel="stylesheet" type="text/css" href="/css/standard.css">
</head>
<body>
<center>
<h1>Current Product Catalog</h1>
<?php 
  if(isset($_GET["addCatalog"])){
    $catalog_inserts=array("product_name"=>$_POST["product_name"],"reference_code"=>$_POST["reference_code"],"design_description"=>$_POST["design_description"],"height"=>$_POST["height"],"width"=>$_POST["width"],"depth"=>$_POST["depth"],"tag_details"=>$_POST["tag_details"],"label_details"=>$_POST["label_details"],"handle_field_1"=>$_POST["handle_field_1"],"sewing_timing"=>$_POST["sewing_timing"],"accessories_timing"=>$_POST["accessories_timing"],"finishing_timing"=>$_POST["finishing_timing"],"catalog_active"=>$_POST["catalog_active"]);
    $query="INSERT INTO ".$database_table_prefix."catalog ";
    $field_string="";
    $value_string="";
    foreach($catalog_inserts as $field=>$value){
      $field_string=$field_string.$field.",";
      if(strlen(trim($value))==0){
        $value_string=$value_string."NULL,";
      }elseif(is_numeric(trim($value))){
        $value_string=$value_string.trim($value).",";
      }else{
        $value_string=$value_string."'".trim($value)."',";
      }
    }
    $field_string=rtrim($field_string,",");
    $value_string=rtrim($value_string,",");
    $query=$query."(".$field_string.") VALUES (".$value_string.");";
    //echo $query;
    $db_conn->query($query);
  }
  if(isset($_GET["updateCatalog"])){
    $catalog_updates=array("product_name"=>$_POST["product_name"],"reference_code"=>$_POST["reference_code"],"design_description"=>$_POST["design_description"],"height"=>$_POST["height"],"width"=>$_POST["width"],"depth"=>$_POST["depth"],"tag_details"=>$_POST["tag_details"],"label_details"=>$_POST["label_details"],"handle_field_1"=>$_POST["handle_field_1"],"sewing_timing"=>$_POST["sewing_timing"],"accessories_timing"=>$_POST["accessories_timing"],"finishing_timing"=>$_POST["finishing_timing"],"catalog_active"=>$_POST["catalog_active"]);
    $query="UPDATE ".$database_table_prefix."catalog SET ";
    foreach($catalog_updates as $field=>$value){
      if(strlen(trim($value))==0){
        $query=$query.$field."=NULL,";
      }elseif(is_numeric(trim($value))){
        $query=$query.$field."=".trim($value).",";
      }else{
        $query=$query.$field."='".trim($value)."',";
      }
    }
    $query=rtrim($query,",");
    $query=$query." WHERE id_catalog=".$_POST["id_catalog"].";";
    //echo $query;
    $db_conn->query($query);
  }
?>
<br>
<button type="button" onclick="window.location='addCatalog.php';">Add to Catalog</button>
<table class="display-table" cellpadding="2" border="1">
<?php
  $query="SELECT * FROM ".$database_table_prefix."catalog ORDER BY product_name,reference_code";
  $result=$db_conn->query($query);
  $headers=$result->fetch_fields();
  echo "<tr>";
  foreach($headers as $field)
    echo "<td>".$field->name."</td>";
  echo "</tr>";
  if($result->num_rows>0){
    while($row=$result->fetch_assoc()){
      echo "<tr>";
      foreach($headers as $field)
        if ($field->name=="product_name")
          echo '<td><a href="editCatalog.php?id='.$row["id_catalog"].'">'.$row[$field->name]."</a></td>";
        elseif(isset($row[$field->name]))
          echo "<td>".$row[$field->name]."</td>";
        else
          echo "<td>NULL</td>";
      echo "</tr>";
    }
  }
?>
</table>
</center>
</body>
</html>
<?php
require_once("../footer.php");
?>