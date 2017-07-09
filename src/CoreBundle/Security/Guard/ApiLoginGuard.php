<?php


namespace CoreBundle\Security\Guard;


use CoreBundle\Entity\User;
use CoreBundle\Model\ResponseModel;
use CoreBundle\Service\JWSService;
use CoreBundle\Service\ResponseSerializer;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Encoder\EncoderFactory;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Component\Security\Guard\AbstractGuardAuthenticator;

class ApiLoginGuard extends AbstractGuardAuthenticator
{
    const EMAIL_FIELD = 'email';

    const PASSWORD_FIELD = 'password';

    /**
     * @var EncoderFactory
     */
    private $encoderFactory;
    /**
     * @var JWSService
     */
    private $JWSService;
    /**
     * @var ResponseSerializer
     */
    private $responseSerializer;

    public function __construct(EncoderFactory $encoderFactory, JWSService $JWSService, ResponseSerializer $responseSerializer)
    {
        $this->encoderFactory = $encoderFactory;
        $this->JWSService = $JWSService;
        $this->responseSerializer = $responseSerializer;
    }

    /**
     * @inheritDoc
     */
    public function getCredentials(Request $request)
    {
        $post = json_decode($request->getContent(), true);

        if ($request->attributes->get('_route') == 'social_login_check' &&
            $request->isMethod(Request::METHOD_POST) &&
            !empty($post[self::EMAIL_FIELD]) &&
            !empty($post[self::PASSWORD_FIELD])
        ) {
            return $post;
        }

        return null;
    }

    /**
     * @inheritDoc
     */
    public function getUser($credentials, UserProviderInterface $userProvider)
    {
        return $userProvider->loadUserByUsername($credentials[self::EMAIL_FIELD]);
    }

    /**
     * @inheritDoc
     */
    public function checkCredentials($credentials, UserInterface $user)
    {
        $encoder = $this->encoderFactory->getEncoder($user);

        return $encoder->isPasswordValid($user->getPassword(), $credentials[self::PASSWORD_FIELD], $user->getSalt());
    }

    /**
     * @inheritDoc
     */
    public function onAuthenticationSuccess(Request $request, TokenInterface $token, $providerKey)
    {
        /** @var User $user */
        $user = $token->getUser();

        $jwsResponse = new ResponseModel($this->JWSService->createJWSTokenModel($user), ResponseModel::JWS_RESPONSE_TYPE);

        return $this->responseSerializer->serializeResponse($jwsResponse, [], Response::HTTP_CREATED);
    }

    /**
     * @inheritDoc
     */
    public function onAuthenticationFailure(Request $request, AuthenticationException $exception)
    {
        return new Response('Authentication Failed.', Response::HTTP_FORBIDDEN);
    }

    /**
     * @inheritDoc
     */
    public function start(Request $request, AuthenticationException $authException = null)
    {
        return new Response('Authentication Required.', Response::HTTP_UNAUTHORIZED);
    }

    /**
     * @inheritDoc
     */
    public function supportsRememberMe()
    {
        return false;
    }

}