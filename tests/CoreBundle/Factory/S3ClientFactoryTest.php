<?php

namespace Tests\CoreBundle\Factory;


use Aws\S3\S3Client;
use PHPUnit\Framework\Assert;
use Tests\BaseTestCase;

class S3ClientFactoryTest extends BaseTestCase
{
    public function testFactory()
    {
        $factory = $this->getContainer()->get('startsymfony.core.s3client_factory');

        Assert::assertInstanceOf(S3Client::class, $factory->createClient());
    }
}