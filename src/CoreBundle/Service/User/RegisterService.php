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
     * This means the user came registered the website
     * @var string
     */
    const SOURCE_TYPE_WEBSITE = 'website';

    /**
     * This means the user registered from the api
     * @var string
     */
    const SOURCE_TYPE_API = 'api';

    /**
     * This means the user registered from the google
     * @var string
     */
    const SOURCE_TYPE_GOOGLE = 'google';

    /**
     * This means the user registered from the facebook
     * @var string
     */
    const SOURCE_TYPE_FACEBOOK = 'facebook';

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
     * @param $source
     *
     * @return User
     */
    public function registerUser(User $user, $source = self::SOURCE_TYPE_WEBSITE)
    {
        $user->setRoles(["ROLE_USER"])
                ->setSource($source)
                ->setEnabled(true);

        $this->userService->saveUserWithPlainPassword($user);
        $this->emailService->sendRegisterEmail($user);
        return $user;
    }


}