<?php

namespace CoreBundle\Security\Provider;

use CoreBundle\Entity\User;
use CoreBundle\Repository\UserRepository;
use CoreBundle\Service\Credential\JWSService;
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
     * @param UserRepository $userRepository
     * @param JWSService $JWSService
     */
    public function __construct(UserRepository $userRepository, JWSService $JWSService)
    {
        parent::__construct($userRepository);
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
        $user = $this->userRepository->find($userId);

        if (empty($user)) {
            throw new UsernameNotFoundException("No user found with id provided.");
        }

        return $user;
    }

}