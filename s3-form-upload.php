<?php

    $AKID = '<ACCESS KEY ID>';
    $secret = '<SECRET ACCESS KEY>';
    $bucket = '<BUCKET>';
    $region = 'eu-west-1';
    $service = 's3';
    $date = date('Ymd');
    $success = 'http://<Success URL'>

    $filename = 'formtest/image.png';
    $amzcreds = "$AKID/$date/$region/$service/aws4_request"; 

    function HMAC($key, $data, $bin = true)
    {
        return hash_hmac('sha256', $data, $key, $bin);
    }
    function createSigningKey($secret, $date, $region, $service)
    {
        $kDate = HMAC("AWS4".$secret, $date);
        $kRegion = HMAC($kDate, $region);
        $kService = HMAC($kRegion, $service);
        $kSigning = HMAC($kService, "aws4_request");
        return $kSigning;
    }

    $policy = base64_encode('{"expiration": "2015-12-121T12:00:00.000Z","conditions": [{"bucket":"'.$bucket.'" },{"acl":"public-read" },{"x-amz-date":"'.$date.'T000000Z"},{"x-amz-credential":"'.$amzcreds.'"},{"x-amz-algorithm":"AWS4-HMAC-SHA256"},{"success_action_redirect":"'.$success.'"},["eq","$key","'.$filename.'"],["starts-with","$Content-Type","image/"],] }');

    $derivedKey = createSigningKey($secret, $date, $region, $service);
    $signature = HMAC($derivedKey, $policy, false);

?>
<html>
  <head>
    
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
    
  </head>
  <body>
  
  <form action="http://<?php echo $bucket;?>.s3-eu-west-1.amazonaws.com/" method="post" enctype="multipart/form-data">
    Key to upload: <br/>
    <input type="input"  name="key" value="<?php echo $filename; ?>" /><br />
    <input type="text"   name="content-type" value="image/png" /><br/>
    <input type="text"   name="X-Amz-Credential" value="<?php echo $amzcreds; ?>" /><br />
    <input type="text"   name="X-Amz-Algorithm" value="AWS4-HMAC-SHA256" /><br/>
    <input type="text"   name="X-Amz-Date" value="<?php echo $date;?>T000000Z" /><br/>
    <input type="hidden" name="success_action_redirect" value="<? echo $success; ?>" />
    <input type="text"   name="acl" value="public-read"/><br />
    <input type="hidden" name="Policy" value="<?php echo $policy; ?>" /><br/>
    <input type="hidden" name="X-Amz-Signature" value="<?php echo $signature; ?>" /><br/>
    File: 
    <input type="file"   name="file" /> <br />
    <input type="submit" name="submit" value="Upload to Amazon S3" />
    <br />
    Derived Key = <?php echo bin2hex($derivedKey); ?>; <br />
    Policy (Base64) = <?php echo $policy?><br /> <br/>
    Signature = <?php echo $signature;?><br /><br />
    Policy (plain text) = <?php echo base64_decode($policy);?>
  </form>  
</html>

