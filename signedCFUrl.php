<?php


require 'aws.phar';

$cf = Aws\CloudFront\CloudFrontClient::factory(array(
'key_pair_id' => 'WhatEver',
'private_key' => 'whatever.pem',
));

$expires = time() + 24 * 3600;

echo "Timestamp:\n";
echo $expires."\n";

$policy = '{
"Statement": [{
"Resource":"http://d3gn4eh04orang.cloudfront.net/Penguins.jpg",
"Condition":{
"DateLessThan":{"AWS:EpochTime":' . $expires . '}
}
}]
}';

echo "\n $policy \n";

var_dump($cf->getSignedUrl(array(
'url' => 'http://d3gn4eh04orang.cloudfront.net/Penguins.jpg',
'policy' => $policy,

)));

?>
