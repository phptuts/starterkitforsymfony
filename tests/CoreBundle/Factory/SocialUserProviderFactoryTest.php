<?php


namespace Tests\CoreBundle\Factory;


use CoreBundle\Security\Provider\FacebookProvider;
use CoreBundle\Security\Provider\GoogleProvider;
use PHPUnit\Framework\Assert;
use Symfony\Component\Intl\Exception\NotImplementedException;
use Tests\BaseTestCase;

class SocialUserProviderFactoryTest extends BaseTestCase
{
    public function testFactory()
    {
        $factory = $this->getContainer()->get('startsymfony.core.user_provider_factory');

        Assert::assertInstanceOf(FacebookProvider::class, $factory->getUserProvider('facebook'));
        Assert::assertInstanceOf(GoogleProvider::class, $factory->getUserProvider('google'));

        $this->expectException(NotImplementedException::class);

        $factory->getUserProvider('github');
    }
}