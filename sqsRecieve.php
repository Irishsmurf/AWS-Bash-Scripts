<?php

require 'vendor/autoload.php';

use Aws\Sqs\SqsClient;

$url = 'https://sqs.eu-west-1.amazonaws.com/ID/SomeQueue';
$num = 10;
$timeout = 15;
$region = 'eu-west-1';

$client = SqsClient::factory(array(
    'region' => $region));

while(true){
    $result = $client->receiveMessage(array(
        'QueueUrl' => $url,
        'MaxNumberOfMessages' => $num,
        'VisibilityTimeout' => $timeout,
        'WaitTimeSeconds' => 20,
        ));

    echo $result;
    foreach($result['Messages'] as $message)
    {
        if(isset($result['MessageId'])){
            $receipt = $message['ReceiptHandle'];
            echo $message['MessageId']."\n";
            $delete = $client->deleteMessage(array(
                'QueueUrl' => $url,
                'ReceiptHandle' => $receipt
                ));
        }
    }
}


?>
