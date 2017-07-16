<?php

namespace CoreBundle\Security\Provider;

use CoreBundle\Entity\User;
use CoreBundle\Service\Credential\JWSService;
use CoreBundle\Service\User\UserService;
use Symfony\Component\Security\Core\Exception\UsernameNotFoundException;

/**
 * Class TokenProvider
 * @package CoreBundle\Security\Provider
 */
class TokenProvider extends AbstractCustomProvider
{
    /**
     * @var JWSService
     */
    private $JWSService;

    /**
     * TokenProvider constructor.
     * @param UserService $userService
     * @param JWSService $JWSService
     */
    public function __construct(UserService $userService, JWSService $JWSService)
    {
        parent::__construct($userService);
        $this->JWSService = $JWSService;
    }

    /**
     * Returns a user from the token paylaod if the token is valid and user_id in the payload is found otherwise throws an exception
     *
     * @param string $username
     * @return null|User
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

        /** @var User $user */
        $user = $this->userService->findUserById($userId);

        if (empty($user)) {
            throw new UsernameNotFoundException("No user found with id provided.");
        }

        return $user;
    }

}