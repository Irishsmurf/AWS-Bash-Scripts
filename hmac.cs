using System;
using System.Collections.Generic;
using System.Linq;
using System.Text;
using System.Threading.Tasks;
using System.Security.Cryptography;



namespace POSTSigV4
{
    class Program
    {
        public static string Base64Encode(string text)
        {
            var textBytes = System.Text.Encoding.UTF8.GetBytes(text);
            return System.Convert.ToBase64String(textBytes);
        }
        public static string ByteArrayToString(byte[] ba)
        {
            string hex = BitConverter.ToString(ba);
            return hex.Replace("-", "");
        }

        static byte[] HmacSHA256(String data, byte[] key)
        {
            String algorithm = "HmacSHA256";
            KeyedHashAlgorithm kha = KeyedHashAlgorithm.Create(algorithm);
            kha.Key = key;

            return kha.ComputeHash(Encoding.UTF8.GetBytes(data));
        }

        static byte[] getSignatureKey(String key, String dateStamp, String regionName, String serviceName)
        {
            byte[] kSecret = Encoding.UTF8.GetBytes(("AWS4" + key).ToCharArray());
            byte[] kDate = HmacSHA256(dateStamp, kSecret);
            byte[] kRegion = HmacSHA256(regionName, kDate);
            byte[] kService = HmacSHA256(serviceName, kRegion);
            byte[] kSigning = HmacSHA256("aws4_request", kService);

            return kSigning;
        }

        static void Main(string[] args)
        {
            string aKey = "<Access Key>";
            string sKey = "<Secret-Access Key>";
            string bucket = "<bucket>";
            string region = "eu-west-1";
            string success = "<Redirect URL>";
            string filename = "formtest/image.png";

            string date = "20141217";
            string xAmzDate = "20141217T000000Z";
            string xAmzCredential = aKey + "/" + date + "/" + region + "/s3/aws4_request";

            byte[] signingKey = getSignatureKey(sKey, date, region, "s3");

            string policy = "{\"expiration\":\"2015-12-121T12:00:00.000Z\",\"conditions\": [{\"bucket\":\""+bucket+"\" },{\"acl\":\"public-read\" },{\"x-amz-date\":\""+xAmzDate+"\"},{\"x-amz-credential\":\""+xAmzCredential+"\"},{\"x-amz-algorithm\":\"AWS4-HMAC-SHA256\"},{\"success_action_redirect\":\""+success+"\"},[\"eq\",\"$key\",\""+filename+"\"],[\"starts-with\",\"$Content-Type\",\"image/\"],] }";
            string derivedKey = ByteArrayToString(signingKey);
            string base64policy = Base64Encode(policy);
            string signature = ByteArrayToString(HmacSHA256(base64policy, signingKey));

            Console.WriteLine("Derived Key =  "+derivedKey);
            Console.WriteLine("Policy (Base64) = "+base64policy);
            Console.WriteLine("Signature ="+ signature);
            Console.ReadKey();
        }
    }
}
