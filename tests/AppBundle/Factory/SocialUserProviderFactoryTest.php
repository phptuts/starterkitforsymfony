<?php


namespace Tests\AppBundle\Factory;


use AppBundle\Security\Provider\FacebookProvider;
use AppBundle\Security\Provider\GoogleProvider;
use AppBundle\Security\Provider\RefreshTokenProvider;
use AppBundle\Security\Provider\TokenProvider;
use PHPUnit\Framework\Assert;
use Symfony\Component\Intl\Exception\NotImplementedException;
use Tests\BaseTestCase;

class SocialUserProviderFactoryTest extends BaseTestCase
{
    /**
     * Testing the user provider factory returns the right provider
     */
    public function testFactory()
    {
        $factory = $this->getContainer()->get('AppBundle\Factory\UserProviderFactory');

        Assert::assertInstanceOf(FacebookProvider::class, $factory->getUserProvider('facebook'));
        Assert::assertInstanceOf(GoogleProvider::class, $factory->getUserProvider('google'));
        Assert::assertInstanceOf(RefreshTokenProvider::class, $factory->getUserProvider('refresh_token'));
        Assert::assertInstanceOf(TokenProvider::class, $factory->getUserProvider('api'));

        $this->expectException(NotImplementedException::class);

        $factory->getUserProvider('github');
    }
}