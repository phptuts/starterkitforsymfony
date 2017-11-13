<?php

namespace StarterKit\StartBundle\Security\Provider;

use StarterKit\StartBundle\Entity\BaseUser;
use StarterKit\StartBundle\Service\AuthTokenService;
use StarterKit\StartBundle\Service\AuthTokenServiceInterface;
use StarterKit\StartBundle\Service\UserServiceInterface;
use Symfony\Component\Security\Core\Exception\UsernameNotFoundException;
use Symfony\Component\Security\Core\User\UserProviderInterface;

/**
 * Class TokenProvider
 * @package StarterKit\StartBundle\Security\Provider
 */
class TokenProvider implements TokenProviderInterface
{
    use CustomProviderTrait;

    /**
     * @var AuthTokenService
     */
    private $authTokenService;

    /**
     * TokenProvider constructor.
     * @param UserServiceInterface $userService
     * @param AuthTokenServiceInterface $authTokenService
     */
    public function __construct(AuthTokenServiceInterface $authTokenService, UserServiceInterface $userService)
    {
        $this->authTokenService = $authTokenService;
        $this->userService = $userService;
    }

    /**
     * Returns a user from the token payload if the token is valid and user_id in the payload is found otherwise
     * throws an exception
     *
     * @param string $username
     * @return null|BaseUser
     */
    public function loadUserByUsername($username)
    {
        if (!$this->authTokenService->isValid($username)) {
            throw new UsernameNotFoundException("Invalid Token.");
        }

        $payload = $this->authTokenService->getPayload($username);

        if (empty($payload[AuthTokenService::USER_ID_KEY])) {
            throw new UsernameNotFoundException("No user_id in token payload");
        }

        $userId = $payload[AuthTokenService::USER_ID_KEY];

        /** @var BaseUser $user */
        $user = $this->userService->findUserById($userId);

        if (empty($user)) {
            throw new UsernameNotFoundException("No user found with id provided.");
        }

        return $user;
    }

}