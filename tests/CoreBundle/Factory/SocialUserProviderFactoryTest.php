<?php


namespace Tests\CoreBundle\Factory;


use CoreBundle\Security\Provider\FacebookProvider;
use CoreBundle\Security\Provider\GoogleProvider;
use CoreBundle\Security\Provider\RefreshTokenProvider;
use CoreBundle\Security\Provider\TokenProvider;
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
        $factory = $this->getContainer()->get('startsymfony.core.user_provider_factory');

        Assert::assertInstanceOf(FacebookProvider::class, $factory->getUserProvider('facebook'));
        Assert::assertInstanceOf(GoogleProvider::class, $factory->getUserProvider('google'));
        Assert::assertInstanceOf(RefreshTokenProvider::class, $factory->getUserProvider('refresh_token'));
        Assert::assertInstanceOf(TokenProvider::class, $factory->getUserProvider('api'));

        $this->expectException(NotImplementedException::class);

        $factory->getUserProvider('github');
    }
}