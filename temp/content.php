<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd"> 
<html>
<head>
<title>Tull and Beverly</title>
<link rel="stylesheet" type="text/css" href="style.css" />
<link rel="shortcut icon" href="img/favicon.ico" />
</head>
<body>
<center><h1>Tull Gearreald & Beverly Cope's Wedding</h1></center> 
<div class="nav_container">
<ul class="nav_bar">
<?php
  $nav_bar_array=array("About Us"=>"about","Wedding Info"=>"schedule","Travel Info"=>"location","Registry"=>"registry","Guest Book"=>"guestbook","R.S.V.P"=>"rsvp");
  foreach($nav_bar_array as $name=>$location){
    echo '<li class="nav_item"><a class="nav_link" href="content.php?page='.$location.'">'.$name.'</a></li>';
  }
?>
</ul>
</div>
<hr>
<?php
  if($_GET["page"]=="rsvp"){
?>
<div class="section_div">

<!--<h2>R.S.V.P.</h2>-->
<br>
<p>Please let us know if you'll be coming or not:</p>
<br><br>
<center>
<a href="?page=rsvp_coming"><button type="button" class="button_class">I'll be there!&nbsp;&nbsp;Save me a seat!</button></a><br><br><br>
<a href="?page=rsvp_webcast"><button type="button" class="button_class">I'll watch the webcast.&nbsp;&nbsp;&nbsp;&nbsp;^_^</button></a><br><br><br>
<a href="?page=rsvp_notcoming"><button type="button" class="button_class">I can't make it.&nbsp;&nbsp;&nbsp;&nbsp;T_T</button></a>
</center>
</div>
<?php
  }
?>
<?php
  if($_GET["page"]=="rsvp_webcast"){
?>
<div class="section_div">
<h2>We're glad you'll be joining us from afar.</h2>
<p>Fill out the information below, and we'll make sure that you get an email a few days before the wedding with information about the webcast.</p><br><br><br>
<script type="text/javascript">var submitted=false;</script>
<iframe name="google_submit" id="google_submit" style="display:none;" onload="if(submitted){window.location.href='?page=rsvp_thankyou';}"></iframe>
<form action="https://docs.google.com/forms/d/18w8PwER5CCiNiDIxrUXaU9nu5xKHzaGWtqfMmEsiYIo/formResponse?embedded=true" method="POST" id="ss-form" target="google_submit" onsubmit="submitted=true;">
<p>Please list the member/s of your party:</p><p>(e.g. Tull Gearreald, Beverly Cope)</p>
<input type="text" name="entry.1958729662" value="" class="text_input" id="entry_1958729662" dir="auto" aria-label="Name Please include names for all in your party " title="">
<p>Please list any emails you would us to send instructions to:</p><p>(e.g. me@gmail.com)</p>
<input type="text" name="entry.1544167385" value="" class="text_input" id="entry_1544167385" dir="auto" aria-label="Food Allergies Please indicate any food allergies and which member/s of your party they belong to " title="">
<input type="hidden" name="draftResponse" value="[,,&quot;-7032856570253494215&quot;]
">
<input type="hidden" name="pageHistory" value="0">
<input type="hidden" name="fbzx" value="-7032856570253494215">
<br><input type="submit" class="button_class" name="submit" value="Submit" id="ss-submit">
</form>
</div>
<?php
  }
?>
<?php
  if($_GET["page"]=="rsvp_thankyou"){
?>
<div class="section_div">
<h2>Thanks for taking the time to R.S.V.P.</h2>
<p>If you'd also like to sign our guestbook, you can click <a href="?page=guestbook">here</a>, or follow the link above.</p>
</div>
<?php
  }
?>
<?php
  if($_GET["page"]=="rsvp_notcoming"){
?>
<div class="section_div">
<h2>We're sorry that you can't make it.</h2>
<p>Please fill out this form so we know you won't be joining us.</p><br><br><br>
<script type="text/javascript">var submitted=false;</script>
<iframe name="google_submit" id="google_submit" style="display:none;" onload="if(submitted){window.location.href='?page=rsvp_thankyou';}"></iframe>
<form action="https://docs.google.com/forms/d/1qTkrSgCDX02rjTW3-SpDjQMbqzb7BCjPkPDe-vbZL7E/formResponse?embedded=true" method="POST" id="ss-form" target="google_submit" onsubmit="submitted=true">
<p>Please list the member/s of your party:</p><p>(e.g. Tull Gearreald, Beverly Cope)</p>
<input type="text" name="entry.395280087" value="" class="text_input" id="entry_395280087" dir="auto" aria-label="Name Please include names for all in your party " title="">
<input type="hidden" name="draftResponse" value="[,,&quot;-3879837809198069356&quot;]
">
<input type="hidden" name="pageHistory" value="0">
<input type="hidden" name="fbzx" value="-3879837809198069356">
<br><input type="submit" class="button_class" name="submit" value="Submit" id="ss-submit">
</form>
</div>
<?php
  }
?>
<?php
  if($_GET["page"]=="rsvp_coming"){
?>
<div class="section_div">
<h2>Glad to hear you're coming!</h2>
<p>Please fill out this form, so we can make sure we have delicious food and a seat for you. :)</p><br><br><br>
<script type="text/javascript">var submitted=false;</script>
<iframe name="google_submit" id="google_submit" style="display:none;" onload="if(submitted){window.location.href='?page=rsvp_thankyou';}"></iframe>
<form action="https://docs.google.com/forms/d/1-eUwluN2_TVCpvMesHSbcmCXpLgEkXMNgxMFMCUJt8c/formResponse?embedded=true" method="POST" id="ss-form" target="google_submit" onsubmit="submitted=true;">
<p>Please list the member/s of your party:</p><p>(e.g. Tull Gearreald, Beverly Cope)</p>
<input type="text" name="entry.929504685" value="" class="text_input" id="entry_929504685" dir="auto" aria-label="Name Please include names for all in your party " title="">
<p>Please indicate any food allergies and which member/s of your party they belong to:</p><p>(e.g. Shellfish - Beverly)</p>
<input type="text" name="entry.401712416" value="" class="text_input" id="entry_401712416" dir="auto" aria-label="Food Allergies Please indicate any food allergies and which member/s of your party they belong to " title="">
<input type="hidden" name="draftResponse" value="[,,&quot;-6159808230166569182&quot;]
">
<input type="hidden" name="pageHistory" value="0">
<input type="hidden" name="fbzx" value="-6159808230166569182">
<br><input type="submit" class="button_class" name="submit" value="Submit" id="ss-submit">
</form>
</div>
<?php
  }
?>
<?php
  if($_GET["page"]=="guestbook_thankyou"){
?>
<div class="section_div">
<h2>Thank you for signing our guestbook</h2>
<p>We appreciate you taking the time to leave a message. Thanks for your wishes.</p>
</div>
<?php
  }
?>
<?php
  if($_GET["page"]=="guestbook"){
?>
<div class="section_div">
<h2>Sign our Guestbook</h2>
<script type="text/javascript">var submitted=false;</script>
<iframe name="google_submit" id="google_submit" style="display:none;" onload="if(submitted){window.location.href='?page=guestbook_thankyou';}"></iframe>
<form action="https://docs.google.com/forms/d/1PlHEBI1XtFrd4mSVtNlFCjyjfUhstrfhBCcaswKv-zo/formResponse?embedded=true" method="POST" id="ss-form" target="google_submit" onsubmit="submitted=true;">
<br>
<p>Name:</p>
<input class="text_input" type="text" name="entry.281575062" value="" id="entry_281575062" dir="auto" aria-label="Name Please enter your first and last name " aria-required="true" required="" title="">
<p>Wishes for the Couple:</p>
<textarea class="text_input" name="entry.1648000099" rows="8" cols="0" id="entry_1648000099" dir="auto" aria-label="Wishes for the Couple  ">Let us know if you have anything you'd like to tell us. :)</textarea>

<input type="hidden" name="draftResponse" value="[,,&quot;-1755446350869641320&quot;]
">
<input type="hidden" name="pageHistory" value="0">
<input type="hidden" name="fbzx" value="-1755446350869641320">
<br><input class="button_class" type="submit" name="submit" value="Submit" id="ss-submit">
</form>
</div>
<?php }
  if($_GET["page"]=="about"){
?>
<div id="about" class="section_div">
<table>
<tr><td class="header_td"><h2>About Tull, the Groom</h2></td>
<td rowspan="2" class="img_td">
<img src="img/about.jpg" class="section_image">
</td></tr>
<tr><td class="text_td">
<p>Tull grew up in Alabama with his parents, Tull and Nely Gearreald. For most of his early life, he spent his time alone reading, playing games, or working on mad scientist experiments with his computers. When he was in middle school, he decided that he wanted to go to MIT, and spent the next several years crafting an application for it, which included a job at a hospital, and many volunteer hours with his church. When he was finally ready to apply, he sent in his application, and his work was rewarded with an acceptance. The following autumn, he headed off to MIT for the next chapter of his life.</p>
</td></tr></table><br><br><hr><br><br><table><tr><td rowspan="2" class="img_td">
<img src="img/about.jpg" class="section_image">
</td><td class="header_td"><h2>About Beverly, the Bride</h2></td></tr>
<tr><td class="text_td">
<p>Beverly grew up in Africa, with her younger sister and loving parents, Bev and Pam Cope.  At the tender age of 13, she decided to leave the comforts of home and hearth to head off to boarding school in Kenya.  While there, the time came for her to start thinking about university, which she discussed with her parents and guidance councellor.  Upon his advice, she applied to MIT and got in.  After much prayer and soul searching with the aid of her parents, she decided to head to MIT for her education.</p>
</td></tr></table><br><br><hr><br><br><table>
<tr><td class="header_td"><h2>About Us, Together</h2></td>
<td rowspan="2" class="img_td">
<img src="img/about.jpg" class="section_image">
</td></tr>
<tr><td class="text_td">
<p>Tull arrived at MIT a year ahead of Beverly, and joined the Baptist Student Fellowship. The following year, Beverly arrived, and joined the same group. Tull didn't notice her for the next two years, until they both became officers of the group, and began interacting regularly. Through the time they spent together, they grew closer, and started dating in Tull's senior year. After Beverly graduated the following year, they both moved to Chicago where Beverly had gotten into medical school. Following a six month trip to India, Tull proposed before their third anniversary, and they are now getting married in April.</p>
</td></tr></table>
</div>
<?php
  }
  if($_GET["page"]=="registry"){
?>
<div class="section_div">
<p>We are a couple that isn't particularly fond of having 'stuff', and we've already got a pretty fully equipped household between the two of us, but we've put the few items that we think we'll find really useful in an Amazon Registry <a href="http://www.amazon.com/registry/wedding/SUPRWC358WJS">here</a>.</p>
<br><p>If you don't see anything interesting in that registry, then you can look below. We have listed a few things that we would like to do over our honeymoon, and you can contribute to those things below.</p>
<form>
<?php
function parseHoneyList($title,$text,$name,$max_quantity,$increment_size,$image_location){
  //open format= item,quantity,name
  $file=fopen("honeyItems.csv","r");
  $total_purchased=0;
  while(!feof($file)){
    $data=fgets($file);
    $data_array=explode(",",$data);
    if($data_array[0]==$name){
      $total_purchased+=intval($data_array[1]);
    }
    else{
      continue;
    }
  }
  echo '<table class="honey_table"><tr><th class="honey_header">'.$title.'</th><th class="honey_header">Description</th><th class="honey_header">Vouchers Requested</th><th class="honey_header">Vouchers Recieved</th><th class="honey_header">Voucher Cost</th><th class="honey_header">Contribution</tr>';
  echo '<tr><td class="honey_cell"><img src="img/'.$image_location.'" width="250"></td><td class="honey_cell">'.$text.'</td><td class="honey_cell">';
  if($max_quantity==0){
    echo "Quite a lot...";
  }else{
    echo $max_quantity;
  }
  echo '</td><td class="honey_cell">';
  if($max_quantity==0){
    echo "N/A";
  }else{
    echo $total_purchased;
  }
  echo'</td><td class="honey_cell">$'.$increment_size.'</td><td class="honey_cell">';
  if($max_quantity==0){
    $max_quantity=20;
    $total_purchased=0;
  }
  if($total_purchased>=$max_quantity){
    echo "Sold Out! :)";
  }else{
    echo '<select name="'.$name.'_quantities[]">';
    echo '<option value="0" selected>None</option>';
    for($i=1;$i<=$max_quantity-$total_purchased;$i++){
      echo '<option value="'.$i.'">'.$i.'</option>';
    }
    echo "</select>";
  }
  echo '<input type="hidden" name="names[]" value="'.$name.'"></td></tr></table>';
}
parseHoneyList("Nights at a Fancy Bed and Breakfast","We've found a suite in a B&B that looks like it will be fun to stay in. :)","honey1",0,25,"BnB.jpg");
parseHoneyList("Archery Classes","Tull says this is fun, and it definitely looks cool. :)","honey2",4,20,"archery.jpg");
parseHoneyList("Chocolate Tour","Includes lots of improbably fancy chocolate tastings.","honey3",2,25,"chocolate.jpg");
parseHoneyList("Second City Tickets","Tickets to Chicago's famous improv comedy group, Second City.","honey5",2,30,"second.jpg");
parseHoneyList("Pottery Class","Beverly says this is fun, and it definitely looks cool. :)","honey6",2,25,"pottery.jpg");
parseHoneyList("Zombie Escape","An attempt to escape from a locked room with a zombie in it. What could possibly go wrong?","honey4",2,28,"zombie.png");
?>

</div>
<?php
    }
?>
<div class="section_div">
<a href="index.php"><button type="button" class="button_class">Back</button></a>
</div>
</body>
</html>