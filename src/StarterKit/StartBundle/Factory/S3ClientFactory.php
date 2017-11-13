<?php

namespace StarterKit\StartBundle\Factory;


use Aws\S3\S3Client;

/**
 * Class S3ClientFactory
 * @package StarterKit\StartBundle\Factory
 */
class S3ClientFactory implements S3ClientFactoryInterface
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

    /**
     * @var string
     */
    private $key;

    /**
     * @var string
     */
    private $secret;


    public function __construct($region, $key, $secret, $apiVersion = self::AMAZON_S3_VERSION)
    {
        $this->region = $region;
        $this->apiVersion = $apiVersion;
        $this->key = $key;
        $this->secret = $secret;
    }

    /**
     * Creates a s3 client
     *
     * @return S3Client
     */
    public function getClient()
    {
        return new S3Client([
            'version' => $this->apiVersion,
            'region' => $this->region,
            'credentials' => [
                'key'    => $this->key,
                'secret' => $this->secret,
            ]
        ]);
    }
}