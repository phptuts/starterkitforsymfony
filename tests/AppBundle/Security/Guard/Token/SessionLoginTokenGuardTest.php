<?php

namespace Test\AppBundle\Security\Guard\Token;

use AppBundle\Entity\User;
use AppBundle\Exception\ProgrammerException;
use AppBundle\Factory\UserProviderFactory;
use AppBundle\Security\Guard\Token\SessionLoginTokenGuard;
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
     * @var SessionLoginTokenGuard|Mock
     */
    protected $sessionLoginGuard;

    /**
     * @var UserProviderFactory|Mock
     */
    protected $userProviderFactory;

    /**
     * @var GuardAuthenticatorHandler|Mock
     */
    protected $guardHandler;

    public function setUp()
    {
        parent::setUp();
        $this->guardHandler = \Mockery::mock(GuardAuthenticatorHandler::class);
        $this->userProviderFactory = \Mockery::mock(UserProviderFactory::class);
        $this->sessionLoginGuard = new SessionLoginTokenGuard($this->userProviderFactory, $this->guardHandler);
    }


    /**
     * This tests a bunch of invalid requests
     * @param Request $request
     * @dataProvider dataProviderForGetCreds
     */
    public function testGetCredentialsOnBadRequests(Request $request)
    {
        Assert::assertNull($this->sessionLoginGuard->getCredentials($request));
    }

    /**
     * Tests that a valid request returns the array
     *
     */
    public function testGetCredentialsOnValidRequest()
    {
        $request = Request::create('/token_login_check', 'POST', [],[],[],[], json_encode(['token' => 'asdfasdf', 'type' => 'google']));
        $request->attributes->set('_route', 'token_login_check');

        $creds = $this->sessionLoginGuard->getCredentials($request);

        Assert::assertEquals($creds['token'], 'asdfasdf');
        Assert::assertEquals($creds['type'], 'google');

    }

    /**
     * Tests that if no social provider is implement a UsernameNotFoundException is thrown
     */
    public function testGetUserOnNonImplementedSocialProvider()
    {
        $this->expectException(UsernameNotFoundException::class);
        $this->expectExceptionCode(ProgrammerException::NO_TOKEN_PROVIDER_IMPLEMENTED);
        $this->userProviderFactory->shouldReceive('getUserProvider')->with('github')->andThrow(new NotImplementedException('not implemented'));
        $this->sessionLoginGuard->getUser(['type' => 'github', 'token' => 'token'], \Mockery::mock(UserProviderInterface::class));
    }

    /**
     * Tests that the user the provider found is return
     */
    public function testGetUserSocialProviderFindsUserWithToken()
    {
        $user = new User();
        $userProviderFacebook = \Mockery::mock(UserInterface::class);
        $userProviderFacebook->shouldReceive('loadUserByUsername')->with('token')->andReturn($user);
        $this->userProviderFactory->shouldReceive('getUserProvider')->with('facebook')->andReturn($userProviderFacebook);
        $returnUser = $this->sessionLoginGuard->getUser(['type' => 'facebook', 'token' => 'token'], \Mockery::mock(UserProviderInterface::class));

        Assert::assertEquals($user, $returnUser);
    }

    /**
     * This should always return true because if the user is found by token then the user is valid
     */
    public function testCheckCredentials()
    {
        Assert::assertTrue($this->sessionLoginGuard->checkCredentials([], new User()));
    }

    /**
     * Tests that start returns a 401 this is called when no creds is provided but required
     */
    public function testStart()
    {
        $response = $this->sessionLoginGuard->start(\Mockery::mock(Request::class), \Mockery::mock(AuthenticationException::class));
        Assert::assertEquals(Response::HTTP_UNAUTHORIZED, $response->getStatusCode());
    }

    /**
     * Tests that it returns a 403 and when auth fails
     */
    public function testAuthenticationFailed()
    {
        $response = $this->sessionLoginGuard->onAuthenticationFailure(\Mockery::mock(Request::class), \Mockery::mock(AuthenticationException::class));
        Assert::assertEquals(Response::HTTP_FORBIDDEN, $response->getStatusCode());
    }

    /**
     * Test that it does not support remember me
     */
    public function testRememberMe()
    {
        Assert::assertFalse($this->sessionLoginGuard->supportsRememberMe());
    }

    /**
     * Tests that the session is authenticated and that 200 is returned when authentication is successful.
     */
    public function testOnAuthenticationSuccess()
    {
        $token = \Mockery::mock(TokenInterface::class);
        $request = \Mockery::mock(Request::class);

        $this->guardHandler->shouldReceive('authenticateWithToken')->once()->with($token, $request);

        $response = $this->sessionLoginGuard->onAuthenticationSuccess($request, $token, 'main');
        Assert::assertEquals(Response::HTTP_OK, $response->getStatusCode());
    }


    public function dataProviderForGetCreds()
    {
        $request = Request::create('/token_login_check', 'POST', [],[],[],[], json_encode(['type' => 'google', 'token' => null]));
        $request->attributes->set('_route', 'token_login_check');


        $request2 = Request::create('/token_login_check', 'POST', [],[],[],[], json_encode(['token' => 'asdfasdf']));
        $request2->attributes->set('_route', 'token_login_check');

        return [
             [Request::create('/bad_end_point', 'POST')],
             [Request::create('/token_login_check', 'GET')],
             [$request],
             [$request2]
        ];
    }

}