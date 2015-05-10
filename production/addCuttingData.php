<?php
require_once("../header.php");

  if(isset($_GET["addData"])&&$_POST["id_product"]){
    $production_data_inserts=array("id_product"=>$_POST["id_product"],"work_date"=>$_POST["clean_work_date"],"cutting_description"=>$_POST["cutting_description"],"cutting_purpose"=>$_POST["cutting_purpose"],"cutting_rejects"=>$_POST["cutting_rejects"],"quantity_cut"=>$_POST["quantity_cut"]);
    $query="INSERT INTO ".$database_table_prefix."cutting_data ";
    $field_string="";
    $value_string="";
    foreach($production_data_inserts as $field=>$value){
      $field_string=$field_string.$field.",";
      if($field=='work_date'){
        $value_string=$value_string."STR_TO_DATE('".$_POST["clean_work_date"]."','%d-%m-%y'),";
      }elseif(strlen(trim($value))==0){
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
    echo $query;
    $db_conn->query($query);
  }
  $query="SELECT ".$database_table_prefix."products.id_product AS id_product,product_name,designation_name,order_number,design_description FROM ".$database_table_prefix."product_production_info LEFT JOIN ".$database_table_prefix."products ON ".$database_table_prefix."products.id_product=".$database_table_prefix."product_production_info.id_product LEFT JOIN ".$database_table_prefix."orders ON ".$database_table_prefix."products.id_order=".$database_table_prefix."orders.id_order LEFT JOIN ".$database_table_prefix."designations ON ".$database_table_prefix."products.id_designation=".$database_table_prefix."designations.id_designation WHERE order_number IS NOT NULL AND product_active=1;";
  $result2=$db_conn->query($query);
  //echo $query;
  $product_array=array();
  while($row=$result2->fetch_assoc()){
    $product_description=$row["order_number"].$row["designation_name"]." - ".$row["product_name"];
    if(isset($row["design_description"])){
      $product_description=$product_description." - ".$row["design_description"];
    }
    $product_array[$row["id_product"]]=$product_description;
  }
?>
<html>
<head>
<title>
Freeset Production Management
</title>
<link rel="stylesheet" type="text/css" href="/css/standard.css">
<script src="../js/validation.js"></script>
</head>
<body>
<a href="../index.php">[Back]</a>
<h1>Production Data Input</h1>
<form name="production_data" method="POST" onsubmit="return validateInputs();" action="?addData">
<table>
<tr><td>Product:</td><td><?php makeGenericOptionList($_POST["id_product"],"id_product",$product_array,false); ?></td></tr>
<tr><?php makeInputTextField("Work Date:","clean_work_date",$_POST);?></tr>
<tr><?php makeInputTextField("Description:","cutting_description",$_POST);?></tr>
<tr><td>Cutting Purpose:</td><td><input type="text" name="cutting_purpose" id="cutting_purpose" style="width:25px"></td></tr>
<tr><?php makeInputTextField("Quantity:","quantity_cut",false);?></tr>
<tr><?php makeInputTextField("Rejects:","cutting_rejects",false);?></tr>
</table>
<input type="submit">
</form>
</body>
</html>
<?php
require_once("../footer.php");
?>