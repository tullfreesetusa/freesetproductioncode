function conditionalDropdown(id_parent,id_child,children_object,parent_matching,child_matching,parent_default,child_default){
  var parent=document.getElementById(id_parent);
  var child=document.getElementById(id_child);
  var parent_value=parent.options[parent.selectedIndex].value;
  var initial_child_value;
  if(child.selectedIndex!=-1){
    initial_child_value=child.options[child.selectedIndex].value;
  }else{
    initial_child_value=0;
  }
  var i=0;
  for (i=child.options.length-1;i>=0;i--){
    child.remove(i);
  }
  for (i=0;i<parent_matching.length;i++){
    if(parent_value==parent_matching[i]){
      var new_child_id=child_matching[i];
      var new_option=document.createElement("OPTION");
      new_option.text=children_object[new_child_id];
      new_option.value=new_child_id;
      child.add(new_option);
    }
  }  
}