<?php

namespace AppBundle\Service\Credential;

use AppBundle\Entity\User;
use AppBundle\Model\Security\CredentialModel;
use AppBundle\Service\User\UserService;

/**
 * Class CredentialModelBuilderService
 * @package AppBundle\Service
 */
class CredentialModelBuilderService
{
    /**
     * @var JWSService
     */
    private $JWSService;

    /**
     * @var UserService
     */
    private $userService;

    /**
     * CredentialModelBuilderService constructor.
     * @param JWSService $JWSService
     * @param UserService $userService
     */
    public function __construct(JWSService $JWSService, UserService $userService)
    {
        $this->JWSService = $JWSService;
        $this->userService = $userService;
    }

    /**
     * Creates a credentials model for the user
     *
     * @param User $user
     * @return CredentialModel
     */
    public function createCredentialModel(User $user)
    {
        $authTokenModel = $this->JWSService->createAuthTokenModel($user);
        $this->userService->updateUserRefreshToken($user);

        return new CredentialModel($user, $authTokenModel, $user->getAuthRefreshModel());
    }
}