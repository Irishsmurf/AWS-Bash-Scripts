<?php

require 'vendor/autoload.php';

use Aws\Ses\SesClient;

$from_name = base64_encode("デビッド·カーナン");
$from = "=?utf-8?B?$from_name?= <dave@domain.ninja>";

$subject = 'これはテストメールです。';
$body = 'これは、テスト電子メールの本文です。그리고 이것은 내가 한국어로 말하고있는 것입니다.
한국 기준으로합니다.
북한은 가장 한국입니다.

때이 끝이 것입니다.';

$client = SesClient::factory(array(
    'profile' => 'default',
    'region' => 'eu-west-1'
));


$result = $client->sendEmail(array(
    'Source' => $from,
//  'Source' => '特力零售集團會員服務中心<jonny@davedkernan.co.uk>',
    'Destination' => array(
        'ToAddresses' => array(
            'Test, <someemail@email.com>'
        )
    ),
    'Message' => array(
        'Subject' => array(
            'Data' => $subject,
            'Charset' => 'utf-8'
        ),
        'Body' => array(
            'Text' => array(
                'Data' => $body,
                'Charset' => 'utf-8'
            )
        )
    )
));

echo $result;

?>
