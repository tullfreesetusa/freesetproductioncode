<?php
require_once("config/settings.php");

date_default_timezone_set("Asia/Kolkata");

session_start();

$db_conn=mysqli_connect($database_location, $database_user, $database_pw,$database_db);
if(!$db_conn){
  die("DB Connection Failed:" . mysqli_connect_error());
}
$no_doctype_pages=array("addAddress.php","orderSearch.php","validation.php","downloadPI.php","addFilter.php");
$pagename=(string)$_SERVER["REQUEST_URI"];
$doctype=true;
foreach($no_doctype_pages as $page){
  if(!(strpos($pagename,$page)===false)){
    $doctype=false;
    break;
  }
}
$doctype_string='<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">'."\n";
if($doctype==true)
  echo $doctype_string;
function makeInputTextField($description,$sql_field,$product_row){
  echo '<td>'.$description.'</td><td><input type="text" name="'.$sql_field.'" id="'.$sql_field.'" value="'.$product_row[$sql_field].'"></td>';
}
function makeGenericTextField($sql_field,$row,$size,$disabled){
  echo '<td><input type="text" size="'.$size.'" name="'.$sql_field.'" id="'.$sql_field.'" value="'.$row[$sql_field].'"';
  if($disabled){
    echo ' disabled';
  }
  echo '></td>';
}
function makeFabricOptionList($sql_field,$product_row,$fabric_array){
  echo '<select id="'.$sql_field.'_fabric" name="'.$sql_field.'_fabric"';
  if(!isset($product_row[$sql_field."_fabric"]))
    echo ' disabled';
  echo'><option value="0"></option>';
  foreach($fabric_array as $id => $fabric){
    echo '<option value="'.$id.'"';
    if($product_row[$sql_field."_fabric"]==$id)
      echo " selected";
    echo '>'.$fabric.'</option>';
  }
  echo '</select>';
}
function makeScreenInputField($sql_field,$product_row){
  echo '<td><input type="text" name="'.$sql_field.'" id="'.$sql_field.'" style="width:25px" maxlength="1" value="';
  if(isset($product_row[$sql_field]))
    echo $product_row[$sql_field];
  else
    echo 0;
  echo '"';
  if(!isset($product_row["id_print"])){
    echo " disabled";
  }
  echo '></td>';
}
function makeColorOptionList($sql_field,$product_row,$color_array){
  echo '<select id="'.$sql_field.'_color" name="'.$sql_field.'_color"';
  if(!isset($product_row[$sql_field."_color"]))
    echo ' disabled';
  echo'><option value="0"></option>';
  foreach($color_array as $id => $color){
    echo '<option value="'.$id.'"';
    if($product_row[$sql_field."_color"]==$id)
      echo " selected";
    echo '>'.$color.'</option>';
  }
  echo '</select>';
}
function makeGenericOptionList($default,$sql_field,$id_array,$null_option){
  echo '<select name="'.$sql_field.'">';
  if($null_option)
    echo '<option value="0"></option>';
  foreach($id_array as $id => $attribute){
    if($sql_field=="id_status"&&($id=='36'||$id=='45'))
      continue;
    echo '<option value="'.$id.'"';
    if($default==$id)
      echo " selected";
    echo '>'.$attribute.'</option>';
  }
  echo '</select>';
}
function makeStatusOptionList($default,$sql_field,$status_rows,$is_validated){
  $option_list='<select name="'.$sql_field.'">';
  foreach($status_rows as $status){
    if($status["status_validated"]==1&&$is_validated==0)
      continue;
    $option_list=$option_list.'<option name="'.$sql_field.'" id="'.$sql_field.'" value="'.$status["id_status"].'"';
    if($default==$status["id_status"])
      $option_list=$option_list." selected";
    $option_list=$option_list.'>'.$status["status_name"].'</option>';
  }
  $option_list=$option_list.'</select>';
  return $option_list;
}
function makeFullOptionList($sql_field,$product_row,$color_array,$fabric_array){
  makeFabricOptionList($sql_field,$product_row,$fabric_array);
  makeColorOptionList($sql_field,$product_row,$color_array);
  echo '<input type="checkbox" name="'.$sql_field.'_enabled" id="'.$sql_field.'_enabled" onclick="disableFabricSelectBox(\''.$sql_field.'\');"';
  if(isset($product_row[$sql_field."_color"])&&isset($product_row[$sql_field."_fabric"]))
    echo ' checked';
  echo '>';
}
function makeAccessoryOptionList($default_accessory,$default_count,$accessory_array){
  echo '<input style="width:25px" type="text" name="accessories_counts[]"';
  if(isset($default_accessory)){
    echo ' value="'.$default_count.'"';
  }
  else{
    echo ' disabled';
  }
  echo '>';
  echo '<select onchange="disableOtherBox()" name="accessories_details[]"';
  if (!isset($default_accessory))
    echo ' disabled';
  echo'><option value="0"></option>';
  foreach($accessory_array as $id => $accessory){
    echo '<option value="'.$id.'"';
    if($default_accessory==$id)
      echo " selected";
    echo '>'.$accessory.'</option>';
  }
  echo '<option value="-1">Other</option>';
  echo '</select>';
  echo '<input type="text" style="visibility:hidden" name="accessories_other[]"';
  if(isset($default_accessory)){
    echo ' value="'.$accessory_array[$id].'"';
  }
  else{
    echo ' disabled';
  }
  echo '>';
  echo '<input type="checkbox" name="accessories_enabled[]" onclick="disableAccessoriesSelectBox();"';
  if(isset($default_accessory))
    echo ' checked';
  echo '>';
}
function isPast($date){
  return($date<time());  
}
?>