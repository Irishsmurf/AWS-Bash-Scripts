<?php 
require 'vendor/autoload.php';

echo "\033[37m";
define('GB', 1024 * 1024 * 1024);
use Aws\S3\S3Client;
ini_set('display_errors',1);
date_default_timezone_set('UTC');
// Instantiate the S3 client with your AWS credentials
$s3Client = S3Client::factory(array(
            'region' => 'eu-west-1',
            'version'     => 'latest'
            ));
// Function to transfer 1 day old files to another bucket
function move_today_files($source_bucket,$source_prefix,$destination_bucket){
    global $s3Client;
    echo $source_bucket;
    $objects = $s3Client->getIterator('ListObjects', array(
                'Bucket' =>  $source_bucket,
                'Prefix' => $source_prefix
                ));
    foreach ($objects as $object){
        $file= $object['Key'];
        $key= $object['Key'];
        $dates= $object['LastModified'];
        echo $dates;
        $size= $object['Size'];
        echo " -- -- File Transfer: ".$file."......(".$size." [".$size/GB." GB] )\n";
                if ($size < 5*GB){ // For the files smaller than 5 GB use copyObject function
                    try {
                        $s3Client->copyObject(array(
                                    'Bucket'     => $destination_bucket,
                                    'Key'        => $file,
                                    'CopySource' => "{$source_bucket}/$key",
                                    ));
                    } catch (Exception $e) {
                        echo $e;
                        echo "\n\033[31mUnable to transfer: ".$file."\033[37m\n";
                    }
                }
                else{ // For the files bigger than 5 GB use copy_part function
                    echo "\033[31m[Big file]\033[37m ";
                    $response = $s3Client->createMultipartUpload([
                                    'Bucket'    => $destination_bucket,
                                    'Key'       => $file]);
                    $upload_id = (string) $response{'UploadId'};
                    echo $upload_id."\n";
                    $transferred = 0;
                    $transfersize = 52428800;
                    $i=1;
                    $parts = array();

                    while($transferred < $size){
                        $last_byte = $transferred+$transfersize;
                        if($last_byte > $size)
                            $last_byte = $size - 1;
                        $response = $s3Client->uploadPartCopy([
                                        'Bucket'            => $destination_bucket,
                                        'CopySource'        => $source_bucket."/".$key,
                                        'CopySourceRange'   => "bytes=".$transferred."-".$last_byte,
                                        'UploadId'          => $upload_id,
                                        'PartNumber'        => $i,
                                        'Key'               => $file
                                ]);
                        $parts[] = array(
                                'PartNumber'    => $i++,
                                'ETag'          => $response['ETag']
                        );
                        // Success?
                        //var_dump($response);
                        $transferred = $transferred+$transfersize;
                    }
                
                    $response = $s3Client->completeMultipartUpload([
                                        'Bucket'            => $destination_bucket,
                                        'Key'               => $file,
                                        'Parts'   =>        $parts,
                                        'UploadId'          => $upload_id
                                ]);
                    // Success?
                    echo $response['Location']."\n";
                }
            }
    return 1;
}
// Daily Backup     

$bucket = 'someBucket';
move_today_files($bucket,"",'anotherbucket');
?>
