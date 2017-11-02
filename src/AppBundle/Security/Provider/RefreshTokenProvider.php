<?php


namespace AppBundle\Security\Provider;

use AppBundle\Entity\RefreshToken;
use AppBundle\Exception\ProgrammerException;
use AppBundle\Service\Credential\RefreshTokenService;
use AppBundle\Service\User\UserService;
use Symfony\Component\Security\Core\Exception\UsernameNotFoundException;

/**
 * Class RefreshTokenProvider
 * @package AppBundle\Security\Provider
 */
class RefreshTokenProvider extends AbstractCustomProvider
{

    /**
     * RefreshTokenProvider constructor.
     * @param UserService $userService
     */
    public function __construct(UserService $userService)
    {
        parent::__construct($userService);
    }

    /**
     * See if the refresh token is valid and if it is it return the user otherwise we throw the UsernameNotFoundException
     *
     * @param string $username the refresh token
     * @return \AppBundle\Entity\User
     */
    public function loadUserByUsername($username)
    {
        $user = $this->userService->findUserByValidRefreshToken($username);

        if (empty($user)) {
            throw new UsernameNotFoundException('No user found with refresh token provided.');
        }

        // This adds time to the refresh token.
        $this->userService->updateUserRefreshToken($user);

        return $user;
    }


}