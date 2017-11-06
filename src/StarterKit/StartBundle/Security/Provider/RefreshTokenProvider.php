<?php

namespace StarterKit\StartBundle\Security\Provider;

use StarterKit\StartBundle\Entity\BaseUser;
use Symfony\Component\Security\Core\Exception\UsernameNotFoundException;
use Symfony\Component\Security\Core\User\UserProviderInterface;

/**
 * Class RefreshTokenProvider
 * @package StarterKit\StartBundle\Security\Provider
 */
class RefreshTokenProvider implements UserProviderInterface
{

    use CustomProviderTrait;

    /**
     * See if the refresh token is valid and if it is it return the user otherwise we throw the UsernameNotFoundException
     *
     * @param string $username the refresh token
     * @return BaseUser
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