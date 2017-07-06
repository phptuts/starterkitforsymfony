<?php

namespace Tests\CoreBundle\Factory;

use Facebook\Facebook;
use PHPUnit\Framework\Assert;
use Tests\BaseTestCase;

class FaceBookClientFactoryTest extends BaseTestCase
{
    public function testFactory()
    {
        $factory = $this->getContainer()->get('startsymfony.core.facebook_client_factory');

        Assert::assertInstanceOf(Facebook::class, $factory->getFacebookClient());
    }
}