# truewallet-autologin พัฒนาโดย  NAVyDESIGn

# วิธีการใช้งาน

<h3><b>การตั้งค่า ตัว GetOtpTruewalletAuto</b></h3>
- ไปตั้งค่า Token ที่ getotp/readsms.php บรรทัดที่ 4 <i>หมายเหตู : Token สามารถหาได้จาก [PushBullet](https://www.pushbullet.com/)</i>
- หลังจากตั้งค่า Token แล้วไปที่ submitlogin.php แล้วตั้งค่า $iden // เราต้อง print_r($sms->DevicesList()) ก่อนเพื่อดู Devices เเล้วทำการเเก้ไขเลข array ให้ตรง <i>หมายเหตู : ถ้าบัญชีเชื่อมแค่ 1 Devices สามารถข้ามขั้นตอนนี้ได้เลย</i>
โค๊ดสำหรับดู Array ของ Devices (แนะนำให้สร้างไฟล์ .php ใหม่เพื่อดู)
```php
<?php
  require "getotp/readsms.php";
  
  $sms = new ReadSMS($access_token);
  print_r($sms->DevicesList());
?>
```
</p>
<h3><b>การตั้งค่า Database</b></h3>
- ให้ import ไฟล์ tw_token.sql จากโฟลเดอร์ DB
</p>
<h3><b>การตั้งค่า ตัว Cronjob</b></h3>
- ไปที่ CronjobAPIWallet/Program.cs และแก้ File Directory ให้ตรงกับที่เรา save ไว้ (ต้องตั้งที่บรรทัด 25 56 87)
- เราสามารถแก้เวลาการเลี้ยง Token ได้โดยแก้เวลาที่บรรทัด 49 <i>หมายเหตู : 1000 = 1 วินาที ค่าเริ่มต้นที่ตั้งให้คือ 7 นาที</i>
</p>
# หน้าตาการแสดงผลเมื่อตั้งค่าถูกต้อง
![Result](https://sv1.picz.in.th/images/2020/07/21/5LsVdQ.png)
</p>
<5><b>----- ขอขอบคุณ -----</b></h5>
<b>GetOtpTruewalletAuto จาก https://github.com/ekkamon/GetOtpTruewalletAuto</b>
</p>
<b>มีปัญหาติดต่อได้ที่Facebook : https://www.facebook.com/nanydesignpage </b>
<b>"หมายเหตู : ทำมาแจกฟรีห้ามนำไปจำหน่ายนะจ๊ะ"<b>
