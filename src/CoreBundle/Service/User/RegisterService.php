<?php

namespace CoreBundle\Service\User;


use CoreBundle\Entity\User;
use CoreBundle\Service\EmailService;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorage;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;

/**
 * Class RegisterService
 * @package CoreBundle\Service\User
 */
class RegisterService
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
     * RegisterService constructor.
     * @param UserService $userService
     * @param EmailService $emailService
     */
    public function __construct(UserService $userService, EmailService $emailService)
    {
        $this->userService = $userService;
        $this->emailService = $emailService;
    }

    /**
     * Registers a new user
     *
     * 1. Creates a new user
     * 2. Logs that new user in
     * 3. Sends registration email
     *
     * @param User $user
     *
     * @return User
     */
    public function registerUser(User $user)
    {
        $user->setRoles(["ROLE_USER"])
                ->setEnabled(true);

        $this->userService->saveUserWithPlainPassword($user);
        $this->emailService->sendRegisterEmail($user);
        return $user;
    }


}