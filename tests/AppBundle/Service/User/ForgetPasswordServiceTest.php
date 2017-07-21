<?php

namespace Tests\AppBundle\Service\User;

use AppBundle\Entity\User;
use AppBundle\Service\EmailService;
use AppBundle\Service\User\ForgetPasswordService;
use AppBundle\Service\User\UserService;
use Mockery\Mock;
use PHPUnit\Framework\Assert;
use Tests\BaseTestCase;

class ForgetPasswordServiceTest extends BaseTestCase
{
    /**
     * @var EmailService|Mock
     */
    protected $emailService;

    /**
     * @var UserService|Mock
     */
    protected $userService;

    /**
     * @var ForgetPasswordService
     */
    protected $forgetPasswordService;

    public function setUp()
    {
        $this->userService = \Mockery::mock(UserService::class);
        $this->emailService = \Mockery::mock(EmailService::class);
        $this->forgetPasswordService = new ForgetPasswordService($this->userService, $this->emailService);
    }

    /**
     * Test that forget password saves the token and expiration date.
     */
    public function testForgetPassword()
    {
        $user = new User();
        $this->emailService
                ->shouldReceive('sendForgetPasswordEmail')
                ->once()
                ->with($user);

        $this->userService
                ->shouldReceive('save')
                ->once()
                ->with($user);

        $this->forgetPasswordService->forgetPassword($user);

        Assert::assertNotEmpty($user->getForgetPasswordExpired());
        Assert::assertNotEmpty($user->getForgetPasswordToken());
    }
}