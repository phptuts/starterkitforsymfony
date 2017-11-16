<?php

namespace StarterKit\StartBundle\Service;

use StarterKit\StartBundle\Entity\BaseUser;
use StarterKit\StartBundle\Model\Response\ResponseAuthenticationModel;
use Symfony\Component\HttpFoundation\Cookie;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;

/**
 * Class CredentialResponseBuilderService
 * @package StarterKit\StartBundle\Service\Credential
 */
class AuthResponseService implements AuthResponseServiceInterface
{
    /***
     * @var string the name of the cookie used to store the auth token.
     */
    const AUTH_COOKIE = 'auth_cookie';

    /**
     * @var AuthTokenService
     */
    private $authTokenService;

    /**
     * @var UserService
     */
    private $userService;

    /**
     * CredentialResponseBuilderService constructor.
     * @param AuthTokenServiceInterface $authTokenService
     * @param UserService $userService
     */
    public function __construct(AuthTokenServiceInterface $authTokenService, UserService $userService)
    {
        $this->authTokenService = $authTokenService;
        $this->userService = $userService;
    }

    /**
     * Creates a json response that will contain new credentials for the user.
     *
     * @param BaseUser $user
     * @return JsonResponse
     */
    public function createAuthResponse(BaseUser $user)
    {
        $user = $this->userService->updateUserRefreshToken($user);
        $responseModel = $this->createResponseAuthModel($user);

        return $this->createResponse($responseModel);
    }

    /**
     * Creates a credentials model for the user
     *
     * @param BaseUser $user
     * @return ResponseAuthenticationModel
     */
    private function createResponseAuthModel(BaseUser $user)
    {
        $authTokenModel = $this->authTokenService->createAuthTokenModel($user);

        return new ResponseAuthenticationModel($user, $authTokenModel, $user->getAuthRefreshModel());
    }

    /**
     * @param ResponseAuthenticationModel $responseModel
     *
     * @return JsonResponse
     */
    private function createResponse(ResponseAuthenticationModel $responseModel)
    {
        $response = new JsonResponse($responseModel->getBody(), Response::HTTP_CREATED);
        $response->headers->setCookie(
            new Cookie(
                self::AUTH_COOKIE,
                $responseModel->getAuthToken(),
                $responseModel->getTokenExpirationTimeStamp(),
                null,
                false,
                false
            )
        );

        return $response;
    }
}