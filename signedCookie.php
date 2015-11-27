<?php

function getSignature($policy, $privateKey)
{
    $signature = '';

    $pkeyid = openssl_get_privatekey($privateKey);
    openssl_sign($policy, $signature, $pkeyid);
    openssl_free_key($pkeyid);

    return safeEncode($signature);
 }

function safeEncode($string)
{
    $encoded = base64_encode($string);
    return str_replace(
        array('+', '=', '/'),
        array('-', '_', '~'),
        $encoded);
}

##############################################################

// Get expires date
$date = new DateTime();
$date->setTimezone(new DateTimeZone("UTC"));
$expires = $date->getTimestamp() + 3600;
$domain = 'paddez.com';

// Get policy
$policy = '{
   "Statement":[
      {
         "Resource":"http://paddez.com/5/*",
         "Condition":{
            "DateLessThan":{ "AWS:EpochTime":'.$expires.' }
         }
      }
   ]
}';
$policy = str_replace(' ', '', $policy);
$policy = str_replace("\r\n", '', $policy);
$policy = safeEncode($policy);

// Get signature
$signature = getSignature($policy, _AWS_STREAMER_PK_);

// Set cookies
setcookie('CloudFront-Policy', $policy, 0, '/', $domain);
setcookie('CloudFront-Signature', $signature, 0, '/', $domain);
setcookie('CloudFront-Key-Pair-Id', _AWS_STREAMER_KEYPAIR_, 0, '/', $domain);
