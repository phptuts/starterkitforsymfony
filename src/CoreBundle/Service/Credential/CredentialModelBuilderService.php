<?php

namespace CoreBundle\Service\Credential;

use CoreBundle\Entity\User;
use CoreBundle\Model\Security\CredentialModel;

/**
 * Class CredentialModelBuilderService
 * @package CoreBundle\Service
 */
class CredentialModelBuilderService
{
    /**
     * @var JWSService
     */
    private $JWSService;

    /**
     * @var RefreshTokenService
     */
    private $refreshTokenService;

    /**
     * CredentialModelBuilderService constructor.
     * @param JWSService $JWSService
     * @param RefreshTokenService $refreshTokenService
     */
    public function __construct(JWSService $JWSService, RefreshTokenService $refreshTokenService)
    {
        $this->JWSService = $JWSService;
        $this->refreshTokenService = $refreshTokenService;
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
        $refreshTokenModel = $this->refreshTokenService
                                    ->createRefreshToken($user)
                                    ->getAuthModel();

        return new CredentialModel($user, $authTokenModel, $refreshTokenModel);
    }
}