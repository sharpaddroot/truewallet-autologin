using System;
using System.IO;
using System.Net;
using System.Threading;

namespace CronjobAPIWallet
{
    class Program
    {
        static void Main(string[] args)
        {
            int count = 0;
            Console.Title = "[Cronjob] -> TRUEWALLET AUTO LOGIN BY NAVyDESIGn";
            Console.WriteLine("[---TRUEWALLET AUTO LOGIN BY NAVyDESIGn---]");
            Thread.Sleep(1000);
            goto requestotp;




        cronjob:
                while (true)
                {
                    string html = string.Empty;
                    string url = @"http://127.0.0.1/class-truewallet-autologin/access_token.php";

                    HttpWebRequest request = (HttpWebRequest)WebRequest.Create(url);
                    request.AutomaticDecompression = DecompressionMethods.GZip;

                    using (HttpWebResponse response = (HttpWebResponse)request.GetResponse())
                    using (Stream stream = response.GetResponseStream())
                    using (StreamReader reader = new StreamReader(stream))
                    {
                        html = reader.ReadToEnd();
                        if (html.IndexOf("UPC-200") > -1)
                        {
                            count++;
                            Console.WriteLine("[" + count + "][Cronjob] -> Token success At " + DateTime.Now.ToString("dd/MM/yyyy HH:mm:ss tt"));
                        }
                        else
                        {
                            Console.WriteLine("[" + count + "][Cronjob] -> Token fail");
                            Thread.Sleep(3000);
                            Console.WriteLine("[NEWLOGIN] -> RequestLoginOTP " + DateTime.Now.ToString("dd/MM/yyyy HH:mm:ss tt"));
                            Thread.Sleep(2000);
                            goto requestotp;
                        }
                    }
                    Thread.Sleep(420000);
                }



        requestotp:
            string login = string.Empty;
            string send_otp = @"http://127.0.0.1/class-truewallet-autologin/requestlogin.php";

            HttpWebRequest requestotp = (HttpWebRequest)WebRequest.Create(send_otp);
            requestotp.AutomaticDecompression = DecompressionMethods.GZip;

            using (HttpWebResponse responseotp = (HttpWebResponse)requestotp.GetResponse())
            using (Stream streamotp = responseotp.GetResponseStream())
            using (StreamReader readerotp = new StreamReader(streamotp))
            {
                Thread.Sleep(5000);

                login = readerotp.ReadToEnd();

                if (login.IndexOf("MAS-200") > -1)
                {
                    Console.WriteLine("[RequestLoginOTP] -> Success! " + DateTime.Now.ToString("dd/MM/yyyy HH:mm:ss tt"));
                    Thread.Sleep(5000);
                    goto submitotp;
                }
                else
                {
                    Console.WriteLine("[RequestLoginOTP] -> Failed! >> [ERROR CODE] -> " + login + " " + DateTime.Now.ToString("dd/MM/yyyy HH:mm:ss tt"));
                    Thread.Sleep(60000);
                    goto requestotp;
                }
            }



        submitotp:
            string revice_code = string.Empty;
            string revice_otp = @"http://127.0.0.1/class-truewallet-autologin/submitlogin.php";

            HttpWebRequest submittoken = (HttpWebRequest)WebRequest.Create(revice_otp);
            submittoken.AutomaticDecompression = DecompressionMethods.GZip;

            using (HttpWebResponse gettoken = (HttpWebResponse)submittoken.GetResponse())
            using (Stream stream = gettoken.GetResponseStream())
            using (StreamReader readeotpcode = new StreamReader(stream))
            {
                Thread.Sleep(10000);

                revice_code = readeotpcode.ReadToEnd();

                if (revice_code.IndexOf("MAS-200") > -1)
                {
                    Console.WriteLine("[SubmitLoginOTP] -> Success! " + DateTime.Now.ToString("dd/MM/yyyy HH:mm:ss tt"));
                    Thread.Sleep(2000);
                    goto cronjob;
                }
                else
                {
                    Console.WriteLine("[SubmitLoginOTP] -> Failed! >> [ERROR CODE] -> " + revice_code + " " + DateTime.Now.ToString("dd/MM/yyyy HH:mm:ss tt"));
                    Thread.Sleep(10000);
                    goto submitotp;
                }
            }







        }
    }
}
