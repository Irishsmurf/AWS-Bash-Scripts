<?php

require 'vendor/autoload.php';

use Aws\Sns\SnsClient;
use Aws\Sqs\SqsClient;

$queueurl = '<SQS Queue>';
$snsapp = '<SNS APP ARN>';

$sqsclient = SqsClient::factory(array(
	'profile' => 'default',
	'region' => 'eu-west-1'
));

$client = SnsClient::factory(array(	
	'profile' => 'default',
	'region' => 'eu-west-1'
));

$result = $sqsclient->receiveMessage(array(
	'QueueUrl' => $queueurl,
	'MaxNumberOfMessages' => 10,
	'WaitTimeSeconds' => 0
));

if(isset($result['Messages']))
{
	$messages = $result['Messages'];
	foreach($messages as $message)
	{
		try{		
			$receipt = $message['ReceiptHandle'];;
			$regid = $message['Body'];
	
			$result = $client->createPlatformEndpoint(array(
				'PlatformApplicationArn' => $snsapp,
				'Token' => $regid,
				'CustomUserData' => $receipt
			));

		}
		catch(Exception $e){
			echo $e->getMessage()."\n";
		}
		
		$result = $sqsclient->deleteMessage(array(
			'QueueUrl' => $queueurl,
			'ReceiptHandle' => $receipt
		));

	}
}



$result = $client->listEndpointsByPlatformApplication(array(
	'PlatformApplicationArn' => $snsapp,
	));
$endpoints = $result['Endpoints'];

foreach($endpoints as $endpoint)
{
	$arn = $endpoint['EndpointArn'];
	$result = $client->setEndpointAttributes(array(
		'EndpointArn' => $arn,
		'Attributes' => array(
			'Enabled' => 'true')
		));

}

foreach($endpoints as $endpoint){
	print_r($endpoint['EndpointArn']."\n");	
	try
	{
		$result = $client->publish(array(
			'TargetArn' => $endpoint['EndpointArn'],
			'Message' => "This is a test from AWS",
		));
	}
	catch(Exception $e)
	{
		echo $e->getMessage()."\n";
	}
	echo "\n\n";
}
?>
