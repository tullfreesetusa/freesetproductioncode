<?php
require_once("../header.php");
  $query="SELECT * FROM ".$database_table_prefix."fabrics WHERE id_fabric IN (1,2)";
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
  $query="SELECT * FROM ".$database_table_prefix."fabrics_colors";
  //echo $query;
  $result3=$db_conn->query($query);
  $matching_array=array();
  while($row=$result3->fetch_assoc()){
    $matching_array[$row["id_fabric"]]=$row["id_color"];
    echo $row["id_fabric"];
  }
  
  $children_object_string='{';
  foreach($color_array as $id=>$name){
    $children_object_string=$children_object_string.$id.':"'.$name.'",';
  }
  $children_object_string=rtrim($children_object_string," ,");
  $children_object_string=$children_object_string.'}';
  $p_match_string='[';
  $c_match_string='[';
  foreach($matching_array as $id_fabric=>$id_color){
    $p_match_string=$p_match_string.$id_fabric.',';
    $c_match_string=$c_match_string.$id_color.',';
  }
  $p_match_string=rtrim($p_match_string," ,");
  $c_match_string=rtrim($c_match_string," ,");
  $p_match_string=$p_match_string.']';
  $c_match_string=$c_match_string.']';
?>
<html>
<head>                         
<title>Test</title>
<script type="text/javascript" src="/js/conditionalDropdown.js"></script>
<script type="text/javascript">
  var children_object=<?php echo $children_object_string; ?>;
  var p_match=<?php echo $p_match_string; ?>;
  var c_match=<?php echo $c_match_string; ?>;
</script>
</head>
<body>
<select id="parent_test" onchange="conditionalDropdown('parent_test','child_test',children_object,p_match,c_match,'','')">
<?php
  foreach($fabric_array as $id=>$name)
    echo '<option value="'.$id.'">'.$name.'</option>';
?>
</select>
<select id="child_test">
</select>
</body>
</html>
<?php
require_once("../footer.php");
?>