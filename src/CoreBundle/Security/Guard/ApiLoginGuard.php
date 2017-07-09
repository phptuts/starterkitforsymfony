<?php


namespace CoreBundle\Security\Guard;


use CoreBundle\Entity\User;
use CoreBundle\Model\Response\ResponseModel;
use CoreBundle\Service\JWSService;
use CoreBundle\Service\ResponseSerializer;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Encoder\EncoderFactory;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Component\Security\Guard\AbstractGuardAuthenticator;

/**
 * Class ApiLoginGuard
 * @package CoreBundle\Security\Guard
 */
class ApiLoginGuard extends AbstractGuardAuthenticator
{
    /**
     * The field where the email
     * @var string
     */
    const EMAIL_FIELD = 'email';

    /**
     * The field where the password
     * @var string
     */
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
     * This validates that the request is a login request and if so returns the email and password do it
     * Otherwise it will return null which will trigger the start method
     *
     * @param $request
     *
     * @return array
     */
    public function getCredentials(Request $request)
    {
        $post = json_decode($request->getContent(), true);

        if ($request->attributes->get('_route') == 'api_login' &&
            $request->isMethod(Request::METHOD_POST) &&
            !empty($post[self::EMAIL_FIELD]) &&
            !empty($post[self::PASSWORD_FIELD])
        ) {
            return $post;
        }

        return null;
    }

    /**
     * This will return a User if one is found.  We pass the email address because we do email only auth.
     *
     * @param array $credentials
     * @param UserProviderInterface $userProvider
     *
     * @return UserInterface
     */
    public function getUser($credentials, UserProviderInterface $userProvider)
    {
        return $userProvider->loadUserByUsername($credentials[self::EMAIL_FIELD]);
    }

    /**
     * Returns true if the password matches the user found in the database
     *
     * @param array $credentials
     * @param UserInterface $user
     * @return bool
     */
    public function checkCredentials($credentials, UserInterface $user)
    {
        $encoder = $this->encoderFactory->getEncoder($user);

        return $encoder->isPasswordValid($user->getPassword(), $credentials[self::PASSWORD_FIELD], $user->getSalt());
    }

    /**
     * This returns a json response with user serialized, token, and the expiration date wrapped in a envelope
     *
     * @param Request $request
     * @param TokenInterface $token
     * @param string $providerKey
     * @return JsonResponse
     */
    public function onAuthenticationSuccess(Request $request, TokenInterface $token, $providerKey)
    {
        /** @var User $user */
        $user = $token->getUser();

        $jwsResponse = new ResponseModel($this->JWSService->createJWSTokenModel($user), ResponseModel::JWS_RESPONSE_TYPE);

        return $this->responseSerializer->serializeResponse($jwsResponse, [User::USER_PERSONAL_SERIALIZATION_GROUP], Response::HTTP_CREATED);
    }

    /**
     * This returns a 403 and happens when the authentication fails
     *
     * @param Request $request
     * @param AuthenticationException $exception
     *
     * @return Response
     */
    public function onAuthenticationFailure(Request $request, AuthenticationException $exception)
    {
        return new Response('Authentication Failed.', Response::HTTP_FORBIDDEN);
    }

    /**
     * This returns a 401 and happens when auth is required but none is provided
     *
     * @param Request $request
     * @param AuthenticationException $authException
     *
     * @return Response
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