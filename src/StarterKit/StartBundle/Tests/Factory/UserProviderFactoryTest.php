<?php


namespace StarterKit\StartBundle\Tests\Factory;


use StarterKit\StartBundle\Factory\UserProviderFactory;
use StarterKit\StartBundle\Security\Provider\FacebookProvider;
use StarterKit\StartBundle\Security\Provider\GoogleProvider;
use StarterKit\StartBundle\Security\Provider\RefreshTokenProvider;
use StarterKit\StartBundle\Security\Provider\TokenProvider;
use StarterKit\StartBundle\Security\Provider\EmailProvider;
use PHPUnit\Framework\Assert;
use Symfony\Component\Intl\Exception\NotImplementedException;
use StarterKit\StartBundle\Tests\BaseTestCase;

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
        $emailProvider = \Mockery::mock(EmailProvider::class);

        $factory = new UserProviderFactory($facebook,$google, $refresh, $token, $emailProvider);

        Assert::assertEquals($facebook, $factory->getUserProvider('facebook'));
        Assert::assertEquals($google, $factory->getUserProvider('google'));
        Assert::assertEquals($refresh, $factory->getUserProvider('refresh_token'));
        Assert::assertEquals($token, $factory->getUserProvider('jwt'));
        Assert::assertEquals($emailProvider, $factory->getUserProvider('email'));

        $this->expectException(NotImplementedException::class);

        $factory->getUserProvider('github');
    }
}