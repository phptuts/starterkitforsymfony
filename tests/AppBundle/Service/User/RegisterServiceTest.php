<?php

namespace Tests\AppBundle\Service\User;

use AppBundle\Entity\User;
use AppBundle\Service\EmailService;
use AppBundle\Service\User\RegisterService;
use AppBundle\Service\User\UserService;
use Mockery\Mock;
use PHPUnit\Framework\Assert;
use Tests\BaseTestCase;

class RegisterServiceTest extends BaseTestCase
{
    /**
     * @var UserService|Mock
     */
    protected $userService;

    /**
     * @var EmailService|Mock
     */
    protected $emailService;

    /**
     * @var RegisterService
     */
    protected $registerService;

    public function setUp()
    {
        $this->userService = \Mockery::mock(UserService::class);
        $this->emailService = \Mockery::mock(EmailService::class);
        $this->registerService = new RegisterService($this->userService, $this->emailService);
    }

    public function testRegisterService()
    {
        $user = new User();
        $this->emailService->shouldReceive('sendRegisterEmail')->once()->with($user);
        $this->userService->shouldReceive('saveUserWithPlainPassword')->once()->with($user);
        $this->registerService->registerUser($user);

        Assert::assertEquals(['ROLE_USER'],$user->getRoles());
        Assert::isTrue($user->isEnabled());
    }
}