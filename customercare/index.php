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
<a href="/..">[Home]</a>
<center>
<h1>Current Order List</h1>
<?php
  if(isset($_GET["updateStatus"])&&isset($_GET["id"])&&isset($_POST["id_status"])){
    $query="UPDATE ".$database_table_prefix."orders SET id_status=".$_POST["id_status"].",last_update_date=CURDATE() WHERE id_order=".$_GET["id"];
    //echo $query;
    $db_conn->query($query);
  }
  if(isset($_GET["updateComment"])&&isset($_GET["id"])&&isset($_POST["order_comments"])){
    $query="UPDATE ".$database_table_prefix."orders SET order_comments='".$_POST["order_comments"]."',last_update_date=CURDATE() WHERE id_order=".$_GET["id"];
    //echo $query;
    $db_conn->query($query);
  } 
  if(isset($_GET["addOrder"])){
    $query="INSERT INTO ".$database_table_prefix."orders (order_number,id_distributor,creation_date,last_update_date) VALUES ('".$_POST["order_number"]."','".$_POST["id_distributor"]."',CURDATE(),CURDATE());";
    //echo $query;
    $db_conn->query($query);
    /*$query="SELECT id_order FROM ".$database_table_prefix."orders WHERE order_number='".$_POST["order_number"]."';";
    echo $query;
    $result2=$db_conn->query($query);
    if($result2->num_rows==1){
      $row=$result2->fetch_assoc();
      echo '<div style="width:200px;background-color:#00CC00;"><h4><a href=editOrder.php?id='.$row["id_order"].'>New Order Created</a></h4></div>';
    }*/
  }
  if(isset($_GET["id_orderlist_filter"]))
    $_SESSION["id_orderlist_filter"]=$_GET["id_orderlist_filter"];
  if(isset($_SESSION["id_orderlist_filter"]))
    $orderlist_filter_preset=$_SESSION["id_orderlist_filter"];
  else
    $orderlist_filter_preset=1;
  $query="SELECT * FROM ".$database_table_prefix."orderlist_filters";
  $result=$db_conn->query($query);
  if($result->num_rows>0){
    while($row=$result->fetch_assoc()){
      $orderlist_filter_rows[]=$row;
      if($row["id_orderlist_filter"]==$orderlist_filter_preset)
        $orderlist_filter_selection=$row["orderlist_filter_value"];
    }
  }
  if(!isset($orderlist_filter_selection)){
    $orderlist_filter_preset=1;
    $orderlist_filter_selection=$orderlist_filter_rows[0]["orderlist_filter_value"];
  }
  
  
  if(isset($_GET["id_distributor_filter"]))
    $_SESSION["id_distributor_filter"]=$_GET["id_distributor_filter"];
  if(isset($_SESSION["id_distributor_filter"])){
    $distributor_filter_preset=$_SESSION["id_distributor_filter"];
  }else{
    $distributor_filter_preset=0;
  }
  $query="SELECT * FROM ".$database_table_prefix."distributors ORDER BY distributor_name";
  $result=$db_conn->query($query);
  if($result->num_rows>0){
    $distributor_rows=array();
    while($row=$result->fetch_assoc()){
      $distributor_rows[]=$row;
      if($row["id_distributor"]==$distributor_filter_preset)
        $distributor_filter_selection=$row["id_distributor"];
    }
  }
  if(!isset($distributor_filter_selection)){
    $distributor_filter_selection=0;
  }
  $order_headers=array("Order #","Customer","Bag Type","Quantity","Dispatch Notes","Ship Date","Last Update","Confirmation Date","Status","Comment");
  $order_data=array("order_number","customer","product_name","order_quantity","dispatch_notes","clean_ship_date","clean_update_date","clean_confirmation_date","status_name","order_comments");
  $query="SELECT *,".$database_table_prefix."orders.id_order AS clean_id_order,DATE_FORMAT(last_update_date,'%d %b. %Y') AS clean_update_date,DATE_FORMAT(ship_date,'%d %b. %Y') AS clean_ship_date, DATE_FORMAT(confirmation_date,'%d %b. %Y') AS clean_confirmation_date FROM ".$database_table_prefix."orders LEFT JOIN ".$database_table_prefix."statuses ON ".$database_table_prefix."statuses.id_status=".$database_table_prefix."orders.id_status LEFT JOIN ".$database_table_prefix."distributors ON ".$database_table_prefix."distributors.id_distributor=".$database_table_prefix."orders.id_distributor LEFT JOIN ".$database_table_prefix."products ON ".$database_table_prefix."products.id_order=".$database_table_prefix."orders.id_order LEFT JOIN ".$database_table_prefix."designations ON ".$database_table_prefix."designations.id_designation=".$database_table_prefix."products.id_designation WHERE (product_active=1 OR product_active IS NULL)";
  //echo $query;
  if(!($orderlist_filter_selection=="None")){
    $query=$query." AND ".$database_table_prefix."orders.id_status NOT IN (".$orderlist_filter_selection.")";
  }
  if(!($distributor_filter_selection==0)){
    $query=$query." AND ".$database_table_prefix."orders.id_distributor=".$distributor_filter_selection;
  }
  $query=$query." ORDER BY -confirmation_date DESC,".$database_table_prefix."products.id_designation";
  //echo $query;
  $result=$db_conn->query($query);
  if($result->num_rows>0){
    $order_rows=array();
    while($row=$result->fetch_assoc()){
      $order_rows[]=$row;
    }
  }
  $query="SELECT * FROM ".$database_table_prefix."statuses ORDER BY status_name";
  $result=$db_conn->query($query);
  if($result->num_rows>0){
    $status_rows=array();
    while($row=$result->fetch_assoc()){
      $status_rows[]=$row;
    }
  }
?>
<form method="post" action="orderSearch.php">
Go-To Order:<input type="text" name="order_number" id="order_number" value="<?php if(isset($_POST["order_number"])){echo $_POST["order_number"];} ?>">
<input type="submit" value="Search">
</form>
<br>
<button type="button" onclick="window.location='addOrder.php';">Add Order</button>
<br>
<br>
<form method="get" name="orderlist_filter_update" onchange="this.submit();" action="index.php">
<a href="listFilters.php">Filters</a>:<select name="id_orderlist_filter">
<?php 
  foreach($orderlist_filter_rows as $filter_row){
    echo '<option value="'.$filter_row["id_orderlist_filter"].'"';
    if($orderlist_filter_preset==$filter_row["id_orderlist_filter"])
      echo ' selected';
    echo '>'.$filter_row["orderlist_filter_name"].'</option>';
  }
?>
</select>
</form>
<form method="get" name="distributor_filter_update" onchange="this.submit();" action="index.php">
Customer Filters:<select name="id_distributor_filter">
<?php
  echo '<option value="0"';
  if($distributor_filter_selection==0)
    echo ' selected';
  echo '>None</option>'; 
  foreach($distributor_rows as $distributor_row){
    echo '<option value="'.$distributor_row["id_distributor"].'"';
    if($distributor_filter_selection==$distributor_row["id_distributor"])
      echo ' selected';
    echo '>'.$distributor_row["distributor_name"].'</option>';
  }
?>
</select>
</form>
<table class="display-table" cellpadding="10">
<?php
  echo "<tr>";
  foreach($order_headers as $header){
    echo "<th>".$header."</th>";
  }
  echo "</tr>";
  foreach($order_rows as $order_row){
    echo '<tr';
    if($order_row["status_color"]!="FFFFFF"){
      echo ' bgcolor="#'.$order_row["status_color"].'">';
    }
    echo '>';
    /*foreach($order_row as $test_key=>$test_value)
      echo $test_key." - ".$test_value;*/
    foreach($order_data as $data){
      $output="";
      if($data=="customer"){
        $output=$order_row["distributor_name"];
        if (isset($order_row["customer_details"])){
          $output=$output." - ".$order_row["customer_details"];
        }
      }elseif($data=="order_number"){
        $output='<a href="orderDetails.php?id='.$order_row["clean_id_order"].'">'.$order_row["order_number"].$order_row["designation_name"].'</a>';
      }elseif($data=="order_comments"){
        $output='<form method="post" action="index.php?updateComment&id='.$order_row["clean_id_order"].'"><input type="text" name="order_comments" value="'.$order_row[$data].'"></form>';
      }elseif($data=="status_name"){
        $output='<form method="post" onchange="this.submit();" action="index.php?updateStatus&id='.$order_row["clean_id_order"].'" name="status_change">'.makeStatusOptionList($order_row["id_status"],"id_status",$status_rows,$order_row["order_validated"])."</form>";
      }else{
        if(strlen(trim($order_row[$data]))==0){
          $output="None"; 
        }
        else
          $output=$order_row[$data];
      }
      echo '<td>'.$output.'</td>';
    }
    echo "</tr>";
  }
  /*if($result->num_rows>0){
    while($row=$result->fetch_assoc()){
      echo "<tr>";
      echo "<td><a href=\"orderDetails.php?id=".$row["id_order"]."\">".$row["order_number"]."</a></td>";
      echo "<td>".$row["distributor_name"];
      if(isset($row["customer_details"]))
        echo " - ".$row["customer_details"];
      echo "</td>";
      echo "<td>".$row["clean_ship_date"]."</td>";
      echo "<td>".$row["clean_confirmation_date"]."</td>";
      echo '<td bgcolor="#'.$row["status_color"].'">'.$row["status_name"]."</td>";
      echo '<td>'.$row["order_comments"].'</td>';
      echo "</tr>";
    }
  }*/
?>
</table>
</center>
</body>
</html>
<?php
require_once("../footer.php");
?>