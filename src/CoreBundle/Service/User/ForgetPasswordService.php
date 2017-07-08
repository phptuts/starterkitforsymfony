<?php

namespace CoreBundle\Service\User;
use CoreBundle\Entity\User;
use CoreBundle\Repository\UserRepository;
use CoreBundle\Service\EmailService;

/**
 * Class ForgetPasswordService
 * @package CoreBundle\Service\User
 */
class ForgetPasswordService
{

    /**
     * @var UserService
     */
    protected $userService;


    /**
     * @var EmailService
     */
    protected $emailService;

    /**
     * ForgetPasswordService constructor.
     * @param UserService $userService
     * @param EmailService $emailService
     */
    public function __construct(UserService $userService, EmailService $emailService)
    {
        $this->userService = $userService;
        $this->emailService = $emailService;
    }

    /**
     * Creates a forget password token and sends a forget password email to the user
     * @param User $user
     */
    public function forgetPassword(User $user)
    {
        $tokenExpires = (new \DateTime())->modify('+2 days');

        $user->setForgetPasswordToken(md5(uniqid(rand(), true)))
            ->setForgetPasswordExpired($tokenExpires);

        $this->userService->save($user);
        $this->emailService->sendForgetPasswordEmail($user);

    }
}