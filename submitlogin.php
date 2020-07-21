<?php

  require "getotp/readsms.php";
  require "twapi/truewallet.php";
  require "system/conn.php";
  
  error_reporting(0);
  $sms = new ReadSMS($access_token);

  $iden = $sms->DevicesList()["devices"][0]["iden"];
  $data = ($sms->GetTrueWalletOTP($iden));
  
  $ref = $data["ref"];
  $otp = $data["otp_code"];

  $tw = new TrueWalletClass("กรอก email", "กรอก password");//Login
  $result = $tw->SubmitLoginOTP($otp, "กรอก เบอร์โทรศัพท์", $ref);

  if($result['data']['access_token'] != null){
    $access_token = $result['data']['access_token'];
  }else{
    $access_token = 0;
  }

  $sql_access_token = "SELECT * FROM access_token ORDER BY token_id DESC LIMIT 1";
  $access_token_query = mysqli_query($dbconnect,$sql_access_token);
  $access_token_record = mysqli_fetch_array($access_token_query);

  if($access_token != $access_token_record['access_token'] && $access_token != 0){
    $insert_access_token = "UPDATE access_token SET access_token= '".$access_token."' WHERE token_id=1";
    $query_img = mysqli_query($dbconnect,$insert_access_token);
  }

  print_r($result['code']);

?>
