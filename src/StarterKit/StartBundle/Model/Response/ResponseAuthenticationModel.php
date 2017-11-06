<?php

namespace StarterKit\StartBundle\Model\Response;


use StarterKit\StartBundle\Entity\User;
use StarterKit\StartBundle\Model\Auth\AuthTokenModel;

class ResponseAuthenticationModel implements ResponseModelInterface
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
     * Returns an array representing the response model
     *
     * @return array
     */
    public function getBody()
    {
        return [
            'meta' => [
                'type' => 'authentication',
                'paginated' => false,
            ],
            'data' => [
                'user' => $this->user->listView(),
                'tokenModel' => $this->tokenModel->getBody(),
                'refreshTokenModel' => $this->refreshTokenModel->getBody()
            ]
        ];
    }

    /**
     * Returns the jwt token
     *
     * @return string
     */
    public function getAuthToken()
    {
        return $this->tokenModel->getToken();
    }

    /**
     * @return integer
     */
    public function getTokenExpirationTimeStamp()
    {
        return $this->tokenModel->getExpirationTimeStamp();
    }

}