# truewallet-autologin พัฒนาโดย  NAVyDESIGn
# วิธีการใช้งาน
## **การตั้งค่า ตัว GetOtpTruewalletAuto**
- ไปตั้งค่า Token ที่ getotp/readsms.php บรรทัดที่ 4 **<i>หมายเหตู : Token สามารถหาได้จาก </i>[PushBullet](https://www.pushbullet.com/)**
- หลังจากตั้งค่า Token แล้วไปที่ submitlogin.php แล้วตั้งค่า $iden 
**<i>หมายเหตู : ถ้าบัญชีเชื่อมแค่ 1 Devices สามารถข้ามขั้นตอนนี้ได้เลย</i>**</p>
**โค๊ดสำหรับดู Array ของ Devices (แนะนำให้สร้างไฟล์ .php ใหม่เพื่อดู)**
```php
<?php
  require "getotp/readsms.php";
  $sms = new ReadSMS($access_token);
  print_r($sms->DevicesList());
?>
```
## **การตั้งค่า Database**
- ให้ import ไฟล์ tw_token.sql จากโฟลเดอร์ DB ลงใน Database ของคุณ
## **การตั้งค่า ตัว Cronjob**
- ไปที่ CronjobAPIWallet/Program.cs และแก้ File Directory ให้ตรงกับที่เรา save ไว้ (ต้องตั้งที่บรรทัด 25 56 87)
- เราสามารถแก้เวลาการเลี้ยง Token ได้โดยแก้เวลาที่บรรทัด 49 <i>หมายเหตู : 1000 = 1 วินาที ค่าเริ่มต้นที่ตั้งให้คือ 7 นาที</i>
## หน้าตาการแสดงผลเมื่อตั้งค่าถูกต้อง
![Result](https://i.imgur.com/AkfSima.png)

## ขอขอบคุณ
**GetOtpTruewalletAuto** จาก https://github.com/ekkamon/GetOtpTruewalletAuto </p>
**มีปัญหาติดต่อได้ที่Facebook : https://www.facebook.com/nanydesignpage </br>
"หมายเหตู : ทำมาแจกฟรีห้ามนำไปจำหน่ายนะจ๊ะ"**
