<?php
require_once("../header.php");
  
  $order_id=$_GET["id"];
  if(isset($_GET["id"])&&is_numeric($_GET["id"])){  
    $query="SELECT order_number, designation_name FROM ".$database_table_prefix."orders LEFT JOIN ".$database_table_prefix."products ON ".$database_table_prefix."orders.id_order=".$database_table_prefix."products.id_order LEFT JOIN ".$database_table_prefix."designations ON ".$database_table_prefix."designations.id_designation=".$database_table_prefix."products.id_designation WHERE ".$database_table_prefix."orders.id_order=".$order_id.";";
    $result=$db_conn->query($query);
    $product_designations=array();
    while($row=$result->fetch_assoc()){
      $order_number=$row["order_number"];
      $used_designations[]=$row["designation_name"];
    }  
    //echo $query."<br>";
    $query="SELECT * FROM ".$database_table_prefix."designations ORDER BY id_designation";
    $result2=$db_conn->query($query);
    $designations_array=array();
    while($row=$result2->fetch_assoc()){
      $designations_array[$row["id_designation"]]=$row["designation_name"];
    }
    //echo $query;
    $query="SELECT * FROM ".$database_table_prefix."catalog WHERE catalog_active=1 ORDER BY product_name,reference_code";
    $result3=$db_conn->query($query);
    $catalog_array=array();
    while($row=$result3->fetch_assoc()){
      $description=$row["product_name"];
      if(isset($row["design_description"])){
        $description=$description.' - '.$row["design_description"];
      }
      $description=$description.' - '.$row["reference_code"];
      $catalog_array[$row["id_catalog"]]=$description;
    }
  }
?>
<html>
<head>
<title>
Add Product - Freeset Order Management
</title>
<link rel="stylesheet" type="text/css" href="/css/standard.css">
</head>
<body>
<a href="orderDetails.php?id=<?php echo $order_id;?>">[Back]</a>
<center>
<h1>Add Product to Order <?php echo $order_number;?></h1>
<form method="post" action="orderDetails.php?id=<?php echo $order_id;?>&addProduct">
<table class="new_product_table" cellpadding="10">
<tr>
<?php
  if(isset($_GET["id"])){
    $found_designation=false;
    $option_string="";
    foreach($designations_array as $id=>$designation){
      if(in_array($designation,$used_designations)){
        continue;
      }
      $found_designation=true;
      $option_string=$option_string.'<option value="'.$id.'">'.$designation.'</option>';
    }
    if($found_designation){
      echo '<tr><td>Designation:</td><td><select id="id_designation" name="id_designation">';
      echo $option_string;
      echo '</option></td></tr>';
    }
    else
      echo '<h2 style="color:red">No designations available. Please add more.</h2>'
?>
</tr>
<tr>
<td>Product:</td>
<td><?php makeGenericOptionList(false,"id_catalog",$catalog_array,true);?><!--<select id="id_catalog" name="id_catalog">
<?php
  foreach($catalog_array as $name=>$reference){
    echo '<option value="'.$name."...".$reference.'">'.$name.' - '.$reference.'</option>';
  }
}
?>--></td>
</tr>
</table>
<input type="submit">
</form>
</center>
</body>
</html>
<?php
require_once("../footer.php");
?>