<?php

namespace StarterKit\StartBundle\Service;

use Aws\Result;
use Aws\S3\S3Client;
use StarterKit\StartBundle\Factory\S3ClientFactory;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class S3Service
{
    /**
     * @var S3Client
     */
    private $client;

    /**
     * @var string
     */
    private $env;

    /**
     * @var string
     */
    private $bucket;

    public function __construct(S3ClientFactory $clientFactory,  $bucket, $env)
    {
        $this->client = $clientFactory->createClient();
        $this->env = $env;
        $this->bucket = $bucket;
    }

    /**
     * Uploads a file to amazon s3 using
     *
     * @param UploadedFile $file
     * @param string $folderPath
     * @param string $fileName
     * @return string
     */
    public function uploadFile(UploadedFile $file, $folderPath, $fileName)
    {

        $folderPath = !empty($folderPath) ?   $folderPath  . '/' : '';
        $path =   $this->env . '/' . $folderPath . $fileName . '.'. $file->guessClientExtension();
        /** @var Result $result */
        $result = $this->client->putObject([
            'ACL' => 'public-read',
            'Bucket' => $this->bucket,
            'SourceFile' => $file->getRealPath(),
            'Key' => $path
        ]);
        
        return $result->get('ObjectURL');
    }
}