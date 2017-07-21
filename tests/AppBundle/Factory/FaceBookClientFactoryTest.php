<?php

namespace Tests\AppBundle\Factory;

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
        $factory = $this->getContainer()->get('AppBundle\Factory\FaceBookClientFactory');

        Assert::assertInstanceOf(Facebook::class, $factory->getFacebookClient());
    }
}