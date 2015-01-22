<?php

require 'vendor/autoload.php';

$emailaddress = 'test@example.com';

use Aws\Ses\SesClient;

$client = SesClient::factory(array(
	'profile' => 'default',
	'region' => 'eu-west-1'
));

$result = $client->verifyEmailAddress(array(
	'EmailAddress' => $emailaddress,
	));

echo $result;

?>
