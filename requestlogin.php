<?php
  require "twapi/truewallet.php";
  
  $tw = new TrueWalletClass("0906571766pumzsp@truemoneywallet.com", "o871651277");//Login
  
  $test = $tw->RequestLoginOTP();//เป็นฟังชั่นขอ otp
  print_r($test['code']);
 
?>