<?php

include('vendor/autoload.php');

use Aws\DynamoDb\DynamoDbClient;


$db = DynamoDbClient::factory(array(
    'profile' => 'default',
    'region' => 'eu-west-1' #replace with your desired region     
));

$result = $db->putItem(array(
'TableName' => 'lastfm-albums',
'Item' => array(
    'mbid'  => array('S' => 'test'),
    'picture-index' => array('S' => 'test'),
    'artist' => array('S' =>  'John'),
    'album' => array('S' => 'Bob')
    )));
