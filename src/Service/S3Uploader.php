<?php

declare(strict_types=1);

namespace App\Service;

use Aws\S3\S3Client;
use Psr\Log\LoggerInterface;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\Filesystem\Filesystem;

class S3Uploader
{
    private $s3Client;

    public function __construct(
        private string $s3BucketKey,
        private string $s3BucketSecret,
        private string $s3BucketName,
        private ParameterBagInterface $params,
        private Filesystem $filesystem,
        private LoggerInterface $logger,
    ) {
        $this->s3Client = new S3Client([
            'region' => 'eu-north-1',
            'version' => 'latest',
            'credentials' => [
                'key' => $this->s3BucketKey,
                'secret' => $this->s3BucketSecret
            ]
        ]);
    }

    public function uploadFile($fileData)
    {
        $bucket = $this->s3BucketName;
        $key = $this->generateKey();
        $filePath = 'uploads/' . $key . '.json';


        try {
            $this->filesystem->dumpFile($filePath, json_encode($fileData));
            $result = $this->s3Client->putObject([
                'Bucket' => $bucket,
                'Key' => $key . '.json',
                'SourceFile' => $filePath
            ]);
        } catch (\Exception $e) {
            $this->logger->error('File uploading failed');
            return false;
        }


        if ($result['@metadata']['statusCode'] !== 200) {
            $this->logger->error('File uploading failed');
            return false;
        }

        $this->filesystem->remove($filePath);
        $this->logger->info('File uploade' . $result['@metadata']['effectiveUri']);
        return true;
    }

    private function generateKey()
    {
        return "file_" . time() . "_" . uniqid();
    }
}