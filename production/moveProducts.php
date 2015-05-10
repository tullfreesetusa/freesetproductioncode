<?php
require_once("../header.php");
  $query="SELECT *,".$database_table_prefix."products.id_product AS clean_id_product FROM ".$database_table_prefix."products LEFT JOIN ".$database_table_prefix."orders ON ".$database_table_prefix."orders.id_order=".$database_table_prefix."products.id_order LEFT JOIN ".$database_table_prefix."statuses ON ".$database_table_prefix."statuses.id_status=".$database_table_prefix."orders.id_status LEFT JOIN ".$database_table_prefix."product_production_info ON ".$database_table_prefix."product_production_info.id_product=".$database_table_prefix."products.id_product LEFT JOIN ".$database_table_prefix."designations ON ".$database_table_prefix."designations.id_designation=".$database_table_prefix."products.id_designation WHERE order_locked=1 AND product_active=1 AND id_product_production IS NULL";
  echo $query;
  $result=$db_conn->query($query);
  $product_rows=array();
  while($row=$result->fetch_assoc()){
    $product_rows[]=$row;
  }
  $table_headers=array("Order","Product","Quantity","How Many Batches","Comment");
  $table_data=array("order_number","product_name","order_quantity","total_batches","production_comment");
?>
<html>
<head>
<title>Freeset Production Management</title>
<link rel="stylesheet" type="text/css" href="/css/standard.css">
</head>
<body>
<a href="index.php">[Back]</a>
<center>
<h1>Move Products into Production</h1>
<form method="post" action="index.php?moveOrders">
<table cellpadding="10">
<?php
  echo '<tr>';
  foreach($table_headers as $header){
    echo '<th>'.$header.'</th>';
  }
  echo '</tr>';
  foreach($product_rows as $product_row){
    echo '<tr>';
    foreach($table_data as $data){
      if ($data=="order_number"){
        $output=$product_row[$data].$product_row["designation_name"].'<input type="hidden" name="id_product[]" value="'.$product_row["clean_id_product"].'">';
      }elseif($data=="product_name"){
        $output=$product_row["product_name"]." - ".$product_row["design_description"];
      }elseif($data=="total_batches"||$data=="production_comment"){
        $output='<input type="text" name="'.$data.'[]" id="'.$data.'">';
      }else{
        $output=$product_row[$data];
      }
      echo '<td>'.$output.'</td>'; 
    }
    echo '</tr>';
  }
?>
</table>
<input type="submit">
</form>
</center>
</body>
</html>
<?php
require_once("../footer.php");
?>