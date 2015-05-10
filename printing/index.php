<?php
require_once("../header.php");
if (isset($_GET["updatePrint"])){
  if(file_exists($_FILES["mockup"]["tmp_name"]) || is_uploaded_file($_FILES["mockup"]["tmp_name"])){
    $target_dir="img/";
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
  $query="UPDATE ".$database_table_prefix."products SET design_description='".$_POST["design_description"]."' ";
  if(isset($mockup_file_location))
    $query=$query.",mockup_location='".$mockup_file_location."',mockup_current=1";
  elseif(!isset($_POST["mockup_unchanged"])){
    $query=$query.",mockup_current=1";
  }
  else
    $query=$query.",mockup_current=0";
  $query=rtrim($query,",");

  $query=$query." WHERE id_product=".$_GET["product_id"].";";
  //$query=mysqli_escape_string($db_conn, $query);
  //echo $query;
  if(isset($_POST["design_description"])){
    $db_conn->query($query);
    if($db_conn->affected_rows==1)
      echo '<center><font color="green">Successful Update</font></center>';
    else
      echo '<center><font color="red">Update Failed</font></center>';
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
  $query="SELECT id_order FROM ".$database_table_prefix."products WHERE id_product=".$_GET["product_id"];
  $order_row=$db_conn->query($query)->fetch_assoc();
  $query="UPDATE ".$database_table_prefix."orders SET order_validated=0 WHERE id_order=".$order_row["id_order"];
  //echo $query;
  $db_conn->query($query);
} 
?>
<html>
<head>
<title>
Freeset Order Management
</title>
<link rel="stylesheet" type="text/css" href="/css/standard.css">
</head>
<body>
<a href="/..">[Home]</a>
<center>
<h1>Current Print List</h1>
<?php
  $product_headers=array("Mockup","Order #","Customer","Bag Name","Quantity","Ship Date","Total Screens","Sides","Status");
  $product_data=array("mockup_location","order_number","customer","product_name","order_quantity","clean_ship_date","num_screens","num_sides","status_name");
  $query="SELECT *,DATE_FORMAT(ship_date,'%d %b. %Y') AS clean_ship_date,IFNULL(front_screens,0)+IFNULL(back_screens,0)+IFNULL(left_screens,0)+IFNULL(right_screens,0)+IFNULL(other_screens,0) AS num_screens,if(front_screens<>0,1,0)+if(back_screens<>0,1,0)+if(left_screens<>0,1,0)+if(right_screens<>0,1,0)+if(other_screens<>0,1,0) AS num_sides FROM ".$database_table_prefix."products RIGHT JOIN ".$database_table_prefix."product_printing ON ".$database_table_prefix."product_printing.id_product=".$database_table_prefix."products.id_product LEFT JOIN ".$database_table_prefix."orders ON ".$database_table_prefix."orders.id_order=".$database_table_prefix."products.id_order LEFT JOIN ".$database_table_prefix."distributors ON ".$database_table_prefix."distributors.id_distributor=".$database_table_prefix."orders.id_distributor LEFT JOIN ".$database_table_prefix."designations ON ".$database_table_prefix."designations.id_designation=".$database_table_prefix."products.id_designation LEFT JOIN ".$database_table_prefix."statuses ON ".$database_table_prefix."orders.id_status=".$database_table_prefix."statuses.id_status WHERE ".$database_table_prefix."orders.id_order IS NOT NULL AND ".$database_table_prefix."products.id_order IS NOT NULL AND ".$database_table_prefix."orders.id_status NOT IN (48,41,40)";
  if(isset($_GET["showAll"]))
    $query="SELECT *,DATE_FORMAT(ship_date,'%d %b. %Y') AS clean_ship_date,IFNULL(front_screens,0)+IFNULL(back_screens,0)+IFNULL(left_screens,0)+IFNULL(right_screens,0)+IFNULL(other_screens,0) AS num_screens,if(front_screens<>0,1,0)+if(back_screens<>0,1,0)+if(left_screens<>0,1,0)+if(right_screens<>0,1,0)+if(other_screens<>0,1,0) AS num_sides FROM ".$database_table_prefix."products LEFT JOIN ".$database_table_prefix."product_printing ON ".$database_table_prefix."product_printing.id_product=".$database_table_prefix."products.id_product LEFT JOIN ".$database_table_prefix."orders ON ".$database_table_prefix."orders.id_order=".$database_table_prefix."products.id_order LEFT JOIN ".$database_table_prefix."distributors ON ".$database_table_prefix."distributors.id_distributor=".$database_table_prefix."orders.id_distributor LEFT JOIN ".$database_table_prefix."designations ON ".$database_table_prefix."designations.id_designation=".$database_table_prefix."products.id_designation LEFT JOIN ".$database_table_prefix."statuses ON ".$database_table_prefix."orders.id_status=".$database_table_prefix."statuses.id_status WHERE ".$database_table_prefix."orders.id_order IS NOT NULL AND ".$database_table_prefix."products.id_order IS NOT NULL AND ".$database_table_prefix."orders.id_status NOT IN (48,41,40) AND id_print IS NULL";
  //echo $query;
  $result=$db_conn->query($query);
  if($result->num_rows>0){
    $product_rows=array();
    while($row=$result->fetch_assoc()){
      $product_rows[]=$row;
    }
  }
?>
<button type="button" onclick="window.location='?showAll';">Add New Prints</button>
<br>
<br>
<table class="display-table" cellpadding="10">
<?php
  echo "<tr>";
  foreach($product_headers as $header){
    echo "<th>".$header."</th>";
  }
  echo "</tr>";
  foreach($product_rows as $product_row){
    foreach($product_data as $data){
      $output="";
      if($data=="customer"){
        $output=$product_row["distributor_name"];
        if (isset($product_row["customer_details"])){
          $output=$output." - ".$product_row["customer_details"];
        }
      }elseif($data=="product_name"){
        $output=$product_row[$data];
        if(isset($product_row["design_description"])){
          $output=$output." - ".$product_row["design_description"];
        }
      }elseif($data=="order_number"){
        $output='<a href="editPrint.php?id='.$product_row["id_product"].'">'.$product_row[$data].$product_row["designation_name"]."</a>";
      }elseif($data=="mockup_location"){
        $output='<img src="/img/'.$product_row[$data].'" height="150">';
        if(!isset($product_row[$data])){
          $output='No Mockup';
        }
      }else{
        $output=$product_row[$data];
      }
      echo '<td>'.$output.'</td>';
    }
    echo "</tr>";
  }
?>
</table>
</center>
</body>
</html>
<?php
require_once("../footer.php");
?>