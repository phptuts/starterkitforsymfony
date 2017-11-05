<?php

namespace AppBundle\Security\Provider;

use AppBundle\Service\UserService;
use Symfony\Component\Security\Core\Exception\UsernameNotFoundException;
use Symfony\Component\Security\Core\User\UserProviderInterface;

/**
 * Class RefreshTokenProvider
 * @package AppBundle\Security\Provider
 */
class RefreshTokenProvider implements UserProviderInterface
{

    use CustomProviderTrait;

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