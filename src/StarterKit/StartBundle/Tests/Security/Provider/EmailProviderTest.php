<?php

namespace StarterKit\StartBundle\Tests\Security\Provider;


use Mockery\Mock;
use PHPUnit\Framework\Assert;
use StarterKit\StartBundle\Security\Provider\EmailProvider;
use StarterKit\StartBundle\Service\UserService;
use StarterKit\StartBundle\Tests\BaseTestCase;
use StarterKit\StartBundle\Tests\Entity\User;

class EmailProviderTest extends BaseTestCase
{
    /**
     * @var UserService|Mock
     */
    protected $userService;

    /**
     * @var EmailProvider
     */
    protected $emailProvider;

    public function setUp()
    {
        parent::setUp();
        $this->userService = \Mockery::mock(UserService::class);
        $this->emailProvider = new EmailProvider($this->userService);
    }

    public function testLoadUserByUsername()
    {
        $user = new User();
        $this->userService->shouldReceive('findUserByEmail')->with('email')->andReturn($user);
        $actualUser = $this->emailProvider->loadUserByUsername('email');
        Assert::assertEquals($user, $actualUser);
    }
}