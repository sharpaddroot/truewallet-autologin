<?php
  require "twapi/truewallet.php";
  require "system/conn.php";

  $tw = new TrueWalletClass("กรอก email", "กรอก password");//Login

  $sql_access_token = "SELECT * FROM access_token WHERE token_id = 1";
  $access_token_query = mysqli_query($dbconnect,$sql_access_token);
  $access_token_record = mysqli_fetch_array($access_token_query);

  $access_token = $access_token_record['access_token'];

  $tw->setAccessToken($access_token);
  $data = $tw->GetProfile();
  print($data["code"]);

?>
