<?php

namespace StarterKit\StartBundle\StarterKit\StartBundle\Tests\Factory;

use StarterKit\StartBundle\Factory\FaceBookClientFactory;
use Facebook\Facebook;
use PHPUnit\Framework\Assert;
use StarterKit\StartBundle\Tests\BaseTestCase;

class FaceBookClientFactoryTest extends BaseTestCase
{
    /**
     * testing facebook factory return the facebook client
     */
    public function testFactory()
    {
        $factory = new FaceBookClientFactory('app_id', 'app_secret', 'v2.9');

        Assert::assertInstanceOf(Facebook::class, $factory->getFacebookClient());
    }
}