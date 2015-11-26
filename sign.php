<?php
    function HMAC($key, $data, $bin = true)
    {
        return hash_hmac('sha256', $data, $key, $bin );
    }

    function createSigningKey($secret, $date, $region, $service)
    {
        $kDate = HMAC("AWS4".$secret, $date);
        $kRegion = HMAC($kDate, $region);
        $kService = HMAC($kRegion, $service);
        $kSigning = HMAC($kService, "aws4_request");
        return $kSigning;
    }

    function getSignature($string, $secret, $date, $region, $service)
    {
        $derivedKey = createSigningKey($secret, $date, $region, $service);
        echo "Derived Key: ";
        $arr = unpack("C*", $derivedKey);
        foreach($arr as $key => $value)
        {
            echo "$value ";
        }
        $signature = HMAC($derivedKey,$string, false);
        echo "\n";
        return $signature;
    }

    $key = 'wJalrXUtnFEMI/K7MDENG+bPxRfiCYEXAMPLEKEY';
    $date = '20110909';
    $region = 'us-east-1';
    $service = 's3';

    $string = "AWS4-HMAC-SHA256\n20110909T233600Z\n20110909/us-east-1/s3/aws4_request\n3511de7e95d28ecd39e9513b642aee07e54f4941150d8df8bf94b328ef7e55e2";
    echo "Signature: ".getSignature($string, $key, $date, $region, $service)."\n";

?>
