<?php

namespace Tests\AppBundle\Factory;

use AppBundle\Factory\FaceBookClientFactory;
use Facebook\Facebook;
use PHPUnit\Framework\Assert;
use Tests\BaseTestCase;

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