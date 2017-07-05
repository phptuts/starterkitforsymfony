<?php

namespace CoreBundle\Factory;


use Aws\S3\S3Client;

/**
 * Class S3ClientFactory
 * @package CoreBundle\Factory
 */
class S3ClientFactory
{
    const AMAZON_S3_VERSION = '2006-03-01';

    /**
     * @var string
     */
    private $region;

    /**
     * @var string
     */
    private $apiVersion;


    public function __construct($region, $apiVersion = self::AMAZON_S3_VERSION)
    {
        $this->region = $region;
        $this->apiVersion = $apiVersion;
    }

    /**
     * Creates a s3 client
     *
     * @return S3Client
     */
    public function createClient()
    {
        return new S3Client([
            'version' => $this->apiVersion,
            'region' => $this->region
        ]);
    }
}