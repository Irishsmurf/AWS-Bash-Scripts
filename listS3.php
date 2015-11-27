<?php

require 'vendor/autoload.php';

use Aws\S3\S3Client;
use Aws\S3\Exception\S3Exception;


$bucket = "somebucket";

$client = S3Client::factory(array(
         'region' => 'eu-west-1'
));

$truncated = false;
$marker = '';
$last = '';
$count = 0;
do
{    

    $objects = $client->listObjects(array(
                'Bucket' => $bucket,
                'Marker' => $marker,
    ));
    foreach($objects['Contents'] as $object){
        $marker = $object['Key'];
        echo $marker."\t".$object['Size']."\n";
        $count++;
    } 
    $truncated = $objects['IsTruncated'];

}while($truncated);


echo "Number of keys: ".$count."\n";
