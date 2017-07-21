<?php


namespace AppBundle\Security\Guard;

use AppBundle\Service\Credential\CredentialResponseBuilderService;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Encoder\EncoderFactory;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Component\Security\Guard\AbstractGuardAuthenticator;

/**
 * The api login guard is for authenticating user through the api that want to login with a email and password
 * This returns back a credentialed response with a jws token / refresh token / user
 *
 * Class ApiLoginGuard
 * @package AppBundle\Security\Guard
 */
class ApiLoginGuard extends AbstractGuardAuthenticator
{

    use GuardTrait;

    use ApiLoginTrait;

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
     * @var CredentialResponseBuilderService
     */
    private $credentialResponseBuilderService;

    /**
     * ApiLoginGuard constructor.
     * @param EncoderFactory $encoderFactory
     * @param CredentialResponseBuilderService $credentialResponseBuilderService
     */
    public function __construct(
        EncoderFactory $encoderFactory,
        CredentialResponseBuilderService $credentialResponseBuilderService
    )
    {
        $this->encoderFactory = $encoderFactory;
        $this->credentialResponseBuilderService = $credentialResponseBuilderService;
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
        $this->getLoginCredentials($request, [self::EMAIL_FIELD, self::PASSWORD_FIELD]);
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