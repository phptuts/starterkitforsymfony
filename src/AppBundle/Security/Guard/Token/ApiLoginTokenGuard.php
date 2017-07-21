<?php

namespace AppBundle\Security\Guard\Token;

use AppBundle\Factory\UserProviderFactory;
use AppBundle\Service\Credential\CredentialResponseBuilderService;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;

/**
 * Class ApiLoginTokenGuard
 * @package AppBundle\Security\Guard\Token
 */
class ApiLoginTokenGuard extends AbstractTokenGuard
{

    /**
     * @var CredentialResponseBuilderService
     */
    protected $credentialResponseBuilderService;

    /**
     * ApiLoginTokenGuard constructor.
     * @param UserProviderFactory $userProviderFactory
     * @param CredentialResponseBuilderService $credentialResponseBuilderService
     */
    public function __construct(
        UserProviderFactory $userProviderFactory,
        CredentialResponseBuilderService $credentialResponseBuilderService
    ) {
        parent::__construct($userProviderFactory);
        $this->credentialResponseBuilderService = $credentialResponseBuilderService;
    }

    /**
     * Gets the token and type for the website users
     *
     * @param Request $request
     *
     * @return array
     */
    public function getCredentials(Request $request)
    {
        $post = json_decode($request->getContent(), true);

        if ($request->getPathInfo() == '/api/login_check' &&
            $request->isMethod(Request::METHOD_POST) &&
            !empty($post[self::TOKEN_FIELD]) &&
            !empty($post[self::TOKEN_TYPE_FIELD])
        ) {
            return $post;
        }

        return null;
    }

    /**
     * This returns a response with the token and refresh token and user serialized in an envelope
     *
     * @param Request $request
     * @param TokenInterface $token
     * @param string $providerKey
     * @return JsonResponse
     */
    public function onAuthenticationSuccess(Request $request, TokenInterface $token, $providerKey)
    {
        return $this->credentialResponseBuilderService->createCredentialResponse($token->getUser());
    }

}