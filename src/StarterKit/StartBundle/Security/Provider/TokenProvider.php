<?php

namespace StarterKit\StartBundle\Security\Provider;

use StarterKit\StartBundle\Entity\BaseUser;
use StarterKit\StartBundle\Service\JWSService;
use StarterKit\StartBundle\Service\UserService;
use Symfony\Component\Security\Core\Exception\UsernameNotFoundException;
use Symfony\Component\Security\Core\User\UserProviderInterface;

/**
 * Class TokenProvider
 * @package StarterKit\StartBundle\Security\Provider
 */
class TokenProvider implements UserProviderInterface
{
    use CustomProviderTrait;

    /**
     * @var JWSService
     */
    private $JWSService;

    /**
     * TokenProvider constructor.
     * @param UserService $userService
     * @param JWSService $JWSService
     */
    public function __construct(JWSService $JWSService, UserService $userService)
    {
        $this->JWSService = $JWSService;
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
        if (!$this->JWSService->isValid($username)) {
            throw new UsernameNotFoundException("Invalid Token.");
        }

        $payload = $this->JWSService->getPayload($username);

        if (empty($payload[JWSService::USER_ID_KEY])) {
            throw new UsernameNotFoundException("No user_id in token payload");
        }

        $userId = $payload[JWSService::USER_ID_KEY];

        /** @var BaseUser $user */
        $user = $this->userService->findUserById($userId);

        if (empty($user)) {
            throw new UsernameNotFoundException("No user found with id provided.");
        }

        return $user;
    }

}