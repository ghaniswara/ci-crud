<?php

namespace App\Storage;

use Aws\S3\S3Client;
use Config\Minio;

class MinioStorage
{
    private static $instance = null;
    public $ArticleBucket = 'pr-articles';
    private $s3Client;

    private function __construct()
    {
        $minioConfig = new Minio();

        $this->s3Client = new S3Client([
            'version' => 'latest',
            'region'  => 'us-east-1',
            'endpoint' => $minioConfig->endpoint,
            'use_path_style_endpoint' => true,
            'credentials' => [
                'key'    => $minioConfig->key,
                'secret' => $minioConfig->secret,
            ],
        ]);
    }

    public static function getInstance(): self
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    private function __clone() {}

    private function __wakeup() {}

    public function putJSON($json, $filename, $bucket)
    {
        try {
            $this->s3Client->putObject([
                'Bucket' => $bucket,
                'Key'    => $filename . '.json',
                'Body'   => $json,
                'ContentType' => 'application/json',
            ]);
        } catch (\Exception $e) {
            throw new \RuntimeException('Error uploading to MinIO: ' . $e->getMessage());
        }
    }

    public function fetchJSON($filename, $bucket)
    {
        try {
            $result = $this->s3Client->getObject([
                'Bucket' => $bucket,
                'Key'    => $filename,
            ]);

            return json_decode($result['Body'], true);
        } catch (\Exception $e) {
            return [];
        }
    }

    public function deleteObject($filename, $bucket)
    {
        $this->s3Client->deleteObject([
            'Bucket' => $bucket,
            'Key'    => $filename,
        ]);
    }

    public function listBucket()
    {
        return $this->s3Client->listBuckets();
    }

    public function listObjects($bucket)
    {
        return $this->s3Client->listObjects([
            'Bucket' => $bucket,
        ]);
    }

    public function createPresignedUrl($filename, $bucket)
    {
        $command = $this->s3Client->getCommand('GetObject', [
            'Bucket' => $bucket,
            'Key'    => $filename,
        ]);

        $request = $this->s3Client->createPresignedRequest($command, '+10 minutes');

        return (string) $request->getUri();
    }
}

