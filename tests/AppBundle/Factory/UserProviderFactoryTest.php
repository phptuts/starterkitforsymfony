<?php


namespace Tests\AppBundle\Factory;


use AppBundle\Factory\UserProviderFactory;
use AppBundle\Security\Provider\FacebookProvider;
use AppBundle\Security\Provider\GoogleProvider;
use AppBundle\Security\Provider\RefreshTokenProvider;
use AppBundle\Security\Provider\TokenProvider;
use PHPUnit\Framework\Assert;
use Symfony\Component\Intl\Exception\NotImplementedException;
use Tests\BaseTestCase;

class UserProviderFactoryTest extends BaseTestCase
{
    /**
     * Testing the user provider factory returns the right provider
     */
    public function testFactory()
    {
        $facebook = \Mockery::mock(FacebookProvider::class);
        $google = \Mockery::mock(GoogleProvider::class);
        $refresh = \Mockery::mock(RefreshTokenProvider::class);
        $token = \Mockery::mock(TokenProvider::class);

        $factory = new UserProviderFactory($facebook,$google, $refresh, $token);

        Assert::assertEquals($facebook, $factory->getUserProvider('facebook'));
        Assert::assertEquals($google, $factory->getUserProvider('google'));
        Assert::assertEquals($refresh, $factory->getUserProvider('refresh_token'));
        Assert::assertEquals($token, $factory->getUserProvider('api'));

        $this->expectException(NotImplementedException::class);

        $factory->getUserProvider('github');
    }
}