<?php

namespace Test\CoreBundle\Security\Guard;

use CoreBundle\Entity\User;
use CoreBundle\Exception\ProgrammerException;
use CoreBundle\Factory\SocialUserProviderFactory;
use CoreBundle\Security\Guard\SessionSocialGuard;
use Mockery\Mock;
use PHPUnit\Framework\Assert;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Intl\Exception\NotImplementedException;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\Exception\UsernameNotFoundException;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Component\Security\Guard\GuardAuthenticatorHandler;
use Tests\BaseTestCase;

class SessionSocialGuardTest extends BaseTestCase
{
    /**
     * @var SessionSocialGuard|Mock
     */
    protected $sessionSocialGuard;

    /**
     * @var SocialUserProviderFactory|Mock
     */
    protected $socialUserProviderFactory;

    /**
     * @var GuardAuthenticatorHandler|Mock
     */
    protected $guardHandler;

    public function setUp()
    {
        parent::setUp();
        $this->guardHandler = \Mockery::mock(GuardAuthenticatorHandler::class);
        $this->socialUserProviderFactory = \Mockery::mock(SocialUserProviderFactory::class);
        $this->sessionSocialGuard = new SessionSocialGuard($this->socialUserProviderFactory, $this->guardHandler);
    }

    /**
     * Tests that the service definition is setup right
     */
    public function testServiceDefinition()
    {
        Assert::assertInstanceOf(SessionSocialGuard::class, $this->getContainer()->get('startsymfony.core.security.session_social_guard'));
    }

    /**
     * This tests a bunch of invalid requests
     * @param Request $request
     * @dataProvider dataProviderForGetCreds
     */
    public function testGetCredentialsOnBadRequests(Request $request)
    {
        Assert::assertNull($this->sessionSocialGuard->getCredentials($request));
    }

    /**
     * Tests that a valid request returns the array
     *
     */
    public function testGetCredentialsOnValidRequest()
    {
        $request = Request::create('/social_login_check', 'POST', [],[],[],[], json_encode(['token' => 'asdfasdf', 'social_type' => 'google']));
        $request->attributes->set('_route', 'social_login_check');

        $creds = $this->sessionSocialGuard->getCredentials($request);

        Assert::assertEquals($creds['token'], 'asdfasdf');
        Assert::assertEquals($creds['social_type'], 'google');

    }

    /**
     * Tests that if no social provider is implement a UsernameNotFoundException is thrown
     */
    public function testGetUserOnNonImplementedSocialProvider()
    {
        $this->expectException(UsernameNotFoundException::class);
        $this->expectExceptionCode(ProgrammerException::NO_SOCIAL_PROVIDER_IMPLEMENTED);
        $this->socialUserProviderFactory->shouldReceive('getUserProvider')->with('github')->andThrow(new NotImplementedException('not implemented'));
        $this->sessionSocialGuard->getUser(['social_type' => 'github', 'token' => 'token'], \Mockery::mock(UserProviderInterface::class));
    }

    /**
     * Tests that the user the provider found is return
     */
    public function testGetUserSocialProviderFindsUserWithToken()
    {
        $user = new User();
        $userProviderFacebook = \Mockery::mock(UserInterface::class);
        $userProviderFacebook->shouldReceive('loadUserByUsername')->with('token')->andReturn($user);
        $this->socialUserProviderFactory->shouldReceive('getUserProvider')->with('facebook')->andReturn($userProviderFacebook);
        $returnUser = $this->sessionSocialGuard->getUser(['social_type' => 'facebook', 'token' => 'token'], \Mockery::mock(UserProviderInterface::class));

        Assert::assertEquals($user, $returnUser);
    }

    /**
     * This should always return true because if the user is found by token then the user is valid
     */
    public function testCheckCredentials()
    {
        Assert::assertTrue($this->sessionSocialGuard->checkCredentials([], new User()));
    }

    /**
     * Tests that start returns a 401 this is called when no creds is provided but required
     */
    public function testStart()
    {
        $response = $this->sessionSocialGuard->start(\Mockery::mock(Request::class), \Mockery::mock(AuthenticationException::class));
        Assert::assertEquals(Response::HTTP_UNAUTHORIZED, $response->getStatusCode());
    }

    /**
     * Tests that it returns a 403 and when auth fails
     */
    public function testAuthenticationFailed()
    {
        $response = $this->sessionSocialGuard->onAuthenticationFailure(\Mockery::mock(Request::class), \Mockery::mock(AuthenticationException::class));
        Assert::assertEquals(Response::HTTP_FORBIDDEN, $response->getStatusCode());
    }

    /**
     * Test that it does not support remember me
     */
    public function testRememberMe()
    {
        Assert::assertFalse($this->sessionSocialGuard->supportsRememberMe());
    }

    /**
     * Tests that the session is authenticated and that 200 is returned when authentication is successful.
     */
    public function testOnAuthenticationSuccess()
    {
        $token = \Mockery::mock(TokenInterface::class);
        $request = \Mockery::mock(Request::class);

        $this->guardHandler->shouldReceive('authenticateWithToken')->once()->with($token, $request);

        $response = $this->sessionSocialGuard->onAuthenticationSuccess($request, $token, 'main');
        Assert::assertEquals(Response::HTTP_OK, $response->getStatusCode());
    }


    public function dataProviderForGetCreds()
    {
        $request = Request::create('/social_login_check', 'POST', [],[],[],[], json_encode(['social_type' => 'google', 'token' => null]));
        $request->attributes->set('_route', 'social_login_check');


        $request2 = Request::create('/social_login_check', 'POST', [],[],[],[], json_encode(['token' => 'asdfasdf']));
        $request2->attributes->set('_route', 'social_login_check');

        return [
             [Request::create('/bad_end_point', 'POST')],
             [Request::create('/social_login_check', 'GET')],
             [$request],
             [$request2]
        ];
    }

}