<?php

require 'vendor/autoload.php';

use Aws\Ec2\Ec2Client;

$ec2Client = Ec2Client::factory(array(
    'profile' => 'default',
    'region' => 'eu-west-1'
    ));


$result = $ec2Client->RunInstances(array(
    'DryRun' => true,
    'ImageId' => 'ami-d7b54da0',
    'MinCount' => 1,
    'MaxCount' => 1,
    'KeyName' => 'SomeKey.pem',
    'SecurityGroupIds' => array('sg-6d0ddb08'),
    'InstanceType' => 't1.micro',
    'BlockDeviceMappings' => array(
        array(
            'DeviceName' => '/dev/sda1',
            'Ebs' => array(
                'VolumeSize' => 8,
                'DeleteOnTermination' => true,
                'ValumeType' => 'standard',
            ),
        ),
    ),
    'SubNetId' => 'subnet-8f57fae4',
    )
); 

var_dump($result);

?>
