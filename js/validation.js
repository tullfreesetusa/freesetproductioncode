function validateInputs(){
  var valid=true;
  var toValidate=["clean_ship_date","clean_cut_date[]","clean_print_date[]","clean_sew_date[]","clean_work_date"];
  var cleanStrings=["Ship Date","Cut Date","Print Date","Sew Date","Work Date"];
  for(var i=0;i<toValidate.length;i++){
    elements=document.getElementsByName(toValidate[i]);
    //console.log("Field being validated:" + toValidate[i]);
    //console.log("Elements found for field:"+elements.length);
    if(elements.length>0){
      if(toValidate[i].indexOf("date")>-1){
        for(var j=0;j<elements.length;j++){
          if((!(parseInt(elements[j].value.substring(0,2))<=31)||!(parseInt(elements[j].value.substring(3,5))<=12)||!(parseInt(elements[j].value.substring(6,8))<=50)||!(elements[j].value.substring(2,3)=="-")||!(elements[j].value.substring(2,3)=="-")||!(elements[j].value.substring(5,6)=="-")||elements[j].value.length!=8) && elements[j].value!=""){
            valid=false;
            alert("Invalid date format in " + cleanStrings[i] + " field.\n Should be dd-mm-yy");
            break;
          }
        }
      }
    }
    else
      continue;
  }
  return valid;
}