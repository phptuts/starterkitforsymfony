<?php


namespace Tests\AppBundle\Factory;


use PHPUnit\Framework\Assert;
use Tests\BaseTestCase;

class GoogleClientFactoryTest extends BaseTestCase
{
    /**
     * Testing the google factory returns the right client
     */
    public function testFactory()
    {
        $factory = $this->getContainer()->get('AppBundle\Factory\GoogleClientFactory');

        Assert::assertInstanceOf(\Google_Client::class, $factory->getGoogleClient());
    }
}