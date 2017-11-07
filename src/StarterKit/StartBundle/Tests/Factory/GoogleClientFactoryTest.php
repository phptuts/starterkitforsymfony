<?php


namespace StarterKit\StartBundle\StarterKit\StartBundle\Tests\Factory;


use StarterKit\StartBundle\Factory\GoogleClientFactory;
use PHPUnit\Framework\Assert;
use StarterKit\StartBundle\Tests\BaseTestCase;

class GoogleClientFactoryTest extends BaseTestCase
{
    /**
     * Testing the google factory returns the right client
     */
    public function testFactory()
    {
        $factory = new GoogleClientFactory('google_client_id');

        Assert::assertInstanceOf(\Google_Client::class, $factory->getGoogleClient());
    }
}