<?php

namespace StarterKit\StartBundle\Tests\Factory;


use StarterKit\StartBundle\Factory\S3ClientFactory;
use Aws\S3\S3Client;
use PHPUnit\Framework\Assert;
use StarterKit\StartBundle\Tests\BaseTestCase;

class S3ClientFactoryTest extends BaseTestCase
{
    /**
     * Testing the s3 factory return s3 cleint
     */
    public function testFactory()
    {
        $factory = new S3ClientFactory('region', 'key', '2006-03-01');

        Assert::assertInstanceOf(S3Client::class, $factory->createClient());
    }
}