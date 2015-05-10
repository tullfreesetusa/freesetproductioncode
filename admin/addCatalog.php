<?php
require_once("../header.php");
  
  $catalog_id=$_GET["id"];
  $query="SELECT * FROM ".$database_table_prefix."catalog WHERE id_catalog=".$catalog_id;
  $result=$db_conn->query($query);
  if($result->num_rows==1){
    $catalog_row=$result->fetch_assoc();
    $order_exists=true;
  }
?>
<html>
<head>
<title>
<?php if($order_exists) echo $catalog_row["order_number"]." - ";?>Freeset Order Management
</title>
<link rel="stylesheet" type="text/css" href="/css/standard.css">
</head>
<body>
<a href="listCatalog.php" class="back_button">[Back]</a>
<center>
<h1>Add New Product to Catalog</h1>
<form action="listCatalog.php?addCatalog" method="post" name="catalog_edit">
<table>
<tr><?php makeInputTextField("Product Name:","product_name",$catalog_row);?>
</tr><tr><?php makeInputTextField("Reference Code:","reference_code",$catalog_row);?>
</tr><tr><?php makeInputTextField("Design Description:","design_description",$catalog_row);?>
</tr><tr><?php makeInputTextField("Height:","height",$catalog_row);?>
</tr><tr><?php makeInputTextField("Width:","width",$catalog_row);?>
</tr><tr><?php makeInputTextField("Depth:","depth",$catalog_row);?>
</tr><tr><?php makeInputTextField("Tag Details:","tag_details",$catalog_row);?>
</tr><tr><?php makeInputTextField("Label Details:","label_details",$catalog_row);?>
</tr><tr><?php makeInputTextField("Handle Type:","handle_field_1",$catalog_row);?>
</tr><tr><?php makeInputTextField("Sewing Timing:","sewing_timing",$catalog_row);?>
</tr><tr><?php makeInputTextField("Accessories Timing:","accessories_timing",$catalog_row);?>
</tr><tr><?php makeInputTextField("Finishing Timing:","finishing_timing",$catalog_row);?>
</tr><tr><td>Active?</td><td><input type="radio" name="catalog_active" value="1"<?php if(!isset($catalog_row["catalog_active"])||$catalog_row["catalog_active"]==1) echo " checked"; ?>>Yes<input type="radio" name="catalog_active" value="0"<?php if(isset($catalog_row["catalog_active"])&&$catalog_row["catalog_active"]==0) echo " checked";?>>No</td>
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