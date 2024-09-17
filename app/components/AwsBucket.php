<?php

namespace app\components;

use Aws\S3\S3Client;
use League\Flysystem\AwsS3V3\AwsS3V3Adapter;
use League\Flysystem\Filesystem;

class AwsBucket
{

    /**
     * Attempt to upload a file to the AWS S3 bucket
     * @param mixed $filePath
     * @param mixed $key
     * @return bool|string
     */
    public static function upload($filePath, $key)
    {
        try {
            $client = new S3Client([
                'version' => 'latest',
                'region' => 'us-east-1',
                'credentials' => [
                    'key' => getenv('AWS_ACCESS_KEY_ID'),
                    'secret' => getenv('AWS_SECRET_ACCESS_KEY'),
                ],
            ]);

            $adapter = new AwsS3V3Adapter($client, getenv('AWS_BUCKET_NAME'));
            $filesystem = new Filesystem($adapter);
            $fileContent = file_get_contents($filePath);
            $filesystem->write($key, $fileContent, ['visibility' => 'public']);
            $path = $client->getObjectUrl(getenv(name: 'AWS_BUCKET_NAME'), $key);

            return $path;
        } catch (\Exception $e) {
            return false;
        }
    }
}
