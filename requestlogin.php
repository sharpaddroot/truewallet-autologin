<?php
  require "twapi/truewallet.php";
  
  $tw = new TrueWalletClass("กรอก email", "กรอก password");//Login
  
  $test = $tw->RequestLoginOTP();//เป็นฟังชั่นขอ otp
  print_r($test['code']);
 
?>
