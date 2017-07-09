<?php


namespace CoreBundle\Model\Security;

use CoreBundle\Entity\User;

/**
 * Class JWSModel
 * @package CoreBundle\Model\User
 */
class CredentialModel
{
    /**
     * The user that the jws token belongs to
     * @var User
     */
    private $user;

    /**
     * @var AuthTokenModel
     */
    private $tokenModel;

    /**
     * @var AuthTokenModel
     */
    private $refreshTokenModel;


    /**
     * JWSModel constructor.
     * @param User $user
     * @param AuthTokenModel $tokenModel
     * @param AuthTokenModel $refreshTokenModel
     */
    public function __construct(User $user, AuthTokenModel $tokenModel, AuthTokenModel $refreshTokenModel)
    {
        $this->user = $user;
        $this->tokenModel = $tokenModel;
        $this->refreshTokenModel = $refreshTokenModel;
    }

    /**
     * @return AuthTokenModel
     */
    public function getTokenModel()
    {
        return $this->tokenModel;
    }

    /**
     * @return AuthTokenModel
     */
    public function getRefreshTokenModel()
    {
        return $this->refreshTokenModel;
    }

    /**
     * @return User
     */
    public function getUser()
    {
        return $this->user;
    }

}