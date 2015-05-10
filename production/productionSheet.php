<?php
require_once("../header.php");
  if(isset($_GET["moveOrders"])){
    $query="INSERT INTO ".$database_table_prefix."product_production_info (id_product,total_batches,production_comment) VALUES (?,?,?)";
    //echo $query;
    $prepared_query=$db_conn->prepare($query);
    $id_product=0;
    $batches=1;
    $comment="";
    $prepared_query->bind_param("iis",$id_product,$batches,$comment);      
    for($i=0;$i<count($_POST["id_product"]);$i++){
      $batches=intval($_POST["total_batches"][$i]);
      $comment=$_POST["production_comment"][$i];
      $id_product=intval($_POST["id_product"][$i]);
      $prepared_query->execute();
    }
  }
  if(isset($_GET["updateInfo"])&&isset($_POST["id_product_production"])){
    $query="UPDATE ".$database_table_prefix."product_production_info SET expected_completion_date=STR_TO_DATE(?,'%d-%m-%y'),cut_date=STR_TO_DATE(?,'%d-%m-%y'),print_date=STR_TO_DATE(?,'%d-%m-%y'),sew_date=STR_TO_DATE(?,'%d-%m-%y'),production_comment=? WHERE id_product_production=?";
    //echo $query;
    $prepared_query=$db_conn->prepare($query);
    $completion_date='11-10-10';
    $cut_date='12-10-10';
    $print_date='13-10-10';
    $sew_date='14-10-10';
    $id_info=1;
    $production_comment="Test";
    $prepared_query->bind_param("sssssi",$completion_date,$cut_date,$print_date,$sew_date,$production_comment,$id_info);
    for($i=0;$i<count($_POST["id_product_production"]);$i++){
      $completion_date=$_POST["clean_expected_date"][$i];
      $cut_date=$_POST["clean_cut_date"][$i];
      $print_date=$_POST["clean_print_date"][$i];
      $sew_date=$_POST["clean_sew_date"][$i];
      $id_info=$_POST["id_product_production"][$i];
      if($cut_date==''){
        $cut_date='NULL';
      }
      if($print_date==''){
        $print_date='NULL';
      }
      if($sew_date==''){
        $sew_date='NULL';
      }
      if($completion_date==''){
        $completion_date='NULL';
      }
      $production_comment=$_POST["production_comment"][$i];
      //echo $id_info;
      $prepared_query->execute();
    }
    
    //for($i=0;$i<$_POST["id_order_production"];$i++)
  }

  $production_pending=false;
  $table_headers=array("Order No.","Product Name","Customer","Quantity","Sewn","Packed","Cut by","Print by","Sew by","Exp. Completion","Ship Date","Status","Comment");
  $table_values=array("order_number","product_name","distributor_name","order_quantity","total_sewn","total_packed","clean_cut_date","clean_print_date","clean_sew_date","clean_expected_date","clean_ship_date","production_status_name","production_comment");
  
  $query="SELECT * FROM ".$database_table_prefix."orders LEFT JOIN ".$database_table_prefix."statuses ON ".$database_table_prefix."statuses.id_status=".$database_table_prefix."orders.id_status LEFT JOIN ".$database_table_prefix."products ON ".$database_table_prefix."products.id_order=".$database_table_prefix."orders.id_order LEFT JOIN ".$database_table_prefix."product_production_info ON ".$database_table_prefix."product_production_info.id_product=".$database_table_prefix."products.id_product WHERE order_locked=1 AND product_active=1 AND id_product_production IS NULL";
  //echo $query;
  $result=$db_conn->query($query);
  if($result->num_rows>0){
    $production_pending=true;
  }
  $query="SELECT *,DATE_FORMAT(ship_date,'%d %b. %y') AS clean_ship_date,DATE_FORMAT(expected_completion_date,'%d-%m-%y') AS clean_expected_date,DATE_FORMAT(sew_date,'%d-%m-%y') AS clean_sew_date,DATE_FORMAT(cut_date,'%d-%m-%y') AS clean_cut_date,DATE_FORMAT(print_date,'%d-%m-%y') AS clean_print_date, SUM(quantity_sewn) AS total_sewn FROM ".$database_table_prefix."products LEFT JOIN ".$database_table_prefix."product_production_info ON ".$database_table_prefix."product_production_info.id_product=".$database_table_prefix."products.id_product LEFT JOIN ".$database_table_prefix."orders ON ".$database_table_prefix."products.id_order=".$database_table_prefix."orders.id_order LEFT JOIN ".$database_table_prefix."production_statuses ON ".$database_table_prefix."product_production_info.id_production_status=".$database_table_prefix."production_statuses.id_production_status LEFT JOIN ".$database_table_prefix."distributors ON ".$database_table_prefix."distributors.id_distributor=".$database_table_prefix."orders.id_distributor LEFT JOIN ".$database_table_prefix."statuses ON ".$database_table_prefix."statuses.id_status=".$database_table_prefix."orders.id_status LEFT JOIN ".$database_table_prefix."designations ON ".$database_table_prefix."designations.id_designation=".$database_table_prefix."products.id_designation LEFT JOIN ".$database_table_prefix."sewing_data ON ".$database_table_prefix."sewing_data.id_product=".$database_table_prefix."products.id_product GROUP BY db_products.id_product ORDER BY -".$database_table_prefix."production_statuses.id_production_status DESC,-ship_date DESC";
  //echo $query;
  $result=$db_conn->query($query);
  if($result->num_rows>0){
    $products_array=array();
    while($row=$result->fetch_assoc()){
      $products_array[]=$row;
    }
  }
  $query="SELECT * FROM ".$database_table_prefix."production_statuses";
  //echo $query;
  $result=$db_conn->query($query);
  if($result->num_rows>0){
    $production_statuses_array=array();
    while($row=$result->fetch_assoc()){
      $production_statuses_array[$row["id_production_status"]]=$row["production_status_name"];
    }
  }
?>
<html>
<head>
<title>
Freeset Production Sheet
</title>
<link rel="stylesheet" type="text/css" href="/css/standard.css">
<script src="../js/validation.js"></script>
</head>
<body>
<a href="/..">[Home]</a>
<center>
<h1>Production Sheet</h1>
<?php if($production_pending){?>
<h2 style="color:red">There are orders that need to be moved into production.</h2>
<button type="button" onclick="window.location='moveProducts.php';">Set Orders into Production</button>
<?php }?>
<form method="post" action="?updateInfo" onSubmit="return validateInputs();">
<!--<button type="button" onclick="validateInputs();">Test Validation</button>-->
<input type="submit" value="Save Changes">
<table cellpadding="10">
<?php
  echo '<tr>';
  foreach($table_headers as $header){
    echo '<th>'.$header.'</th>';
  }
  echo '</tr>';
  foreach($products_array as $product_row){
    echo '<tr>';
    foreach($table_values as $value){
      $output="";
      if($value=="production_status_name"&&!isset($product_row["id_product_production"])){
        $output="Possible Order";
      }elseif($value=="production_status_name"&&isset($product_row["id_product_production"])){
        $output='<input type="hidden" name="id_product_production[]" value="'.$product_row["id_product_production"].'"><select name="id_production_status[]">';
        foreach($production_statuses_array as $production_status_id=>$production_status_name){
          $output=$output.'<option value="'.$production_status_id.'"';
          if($production_status_id==$product_row["id_production_status"]){
            $output=$output.' selected';
          }
          $output=$output.'>'.$production_status_name.'</option>';
        }
        $output=$output.'</option>';
      }elseif(($value=="clean_cut_date"||$value=="clean_print_date"||$value=="clean_sew_date"||$value=="clean_expected_date")&&isset($product_row["id_product_production"])){
        $output='<input type="text" name="'.$value.'[]" size="7" value="'.$product_row[$value].'">';
      }elseif($value=="distributor_name"){
        if(isset($product_row["customer_details"])){
          $output=$product_row["distributor_name"]." - ".$product_row["customer_details"];
        }else{
          $output=$product_row["distributor_name"];
        }
      }elseif($value=="order_number"){
        $output='<a href="orderDetails.php?id='.$product_row["id_order"].'">'.$product_row[$value].$product_row["designation_name"].'</a>';
      }elseif($value=="product_name"){
        $output='<a href="productDetails.php?id='.$product_row["id_product"].'">'.$product_row[$value].'</a>';
      }elseif($value=="production_comment"&&isset($product_row["id_product_production"])){
        $output='<input type="text" name="'.$value.'[]" value="'.$product_row[$value].'">';
      }else{
        $output=$product_row[$value];
      }
      echo '<td>'.$output.'</td>';
    }
    echo '</tr>';
  }
?>
</table>
</form>
</center>
</body>  
</html>
<?php
require_once("../footer.php");
?>