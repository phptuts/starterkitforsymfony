<?php

namespace StarterKit\StartBundle\Tests\Security\Guard;

use Mockery\Mock;
use PHPUnit\Framework\Assert;
use StarterKit\StartBundle\Event\AuthFailedEvent;
use StarterKit\StartBundle\Event\UserEvent;
use StarterKit\StartBundle\Exception\ProgrammerException;
use StarterKit\StartBundle\Factory\UserProviderFactoryInterface;
use StarterKit\StartBundle\Model\Credential\CredentialEmailModel;
use StarterKit\StartBundle\Model\Credential\CredentialTokenModel;
use StarterKit\StartBundle\Security\Guard\SimpleGuard;
use StarterKit\StartBundle\Service\AuthResponseServiceInterface;
use StarterKit\StartBundle\Tests\BaseTestCase;
use StarterKit\StartBundle\Tests\Entity\User;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Intl\Exception\NotImplementedException;
use Symfony\Component\Security\Core\Encoder\EncoderFactoryInterface;
use Symfony\Component\Security\Core\Encoder\PasswordEncoderInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\Exception\UsernameNotFoundException;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Component\Security\Guard\Token\PostAuthenticationGuardToken;

class SimpleGuardTest extends BaseTestCase
{
    /**
     * @var EncoderFactoryInterface|Mock
     */
    protected $encoderFactory;

    /**
     * @var AuthResponseServiceInterface|Mock
     */
    protected $authResponseService;

    /**
     * @var UserProviderFactoryInterface|Mock
     */
    protected $userProviderFactory;

    /**
     * @var \Twig_Environment|Mock
     */
    protected $twig;

    /**
     * @var SimpleGuard
     */
    protected $guard;

    /**
     * @var EventDispatcherInterface|Mock
     */
    protected $dispatcher;

    public function setUp()
    {
        parent::setUp();
        $this->encoderFactory = \Mockery::mock(EncoderFactoryInterface::class);
        $this->authResponseService = \Mockery::mock(AuthResponseServiceInterface::class);
        $this->userProviderFactory = \Mockery::mock(UserProviderFactoryInterface::class);
        $this->twig = \Mockery::mock(\Twig_Environment::class);
        $this->dispatcher = \Mockery::mock(EventDispatcherInterface::class);

        $this->guard = new SimpleGuard(
            $this->encoderFactory,
            $this->authResponseService,
            $this->userProviderFactory,
            $this->twig,
            $this->dispatcher
        );
    }

    public function testLoginRequestWithToken()
    {
        $json = ['token' => 'tok_3dasdfadfs', 'type' => 'refresh_token'];
        $request = Request::create('/login_check',
            'POST',
            [],
            [],
            [],
            [],
            json_encode($json)
        );

        $request->headers->set('Content-Type', 'application/json');

        $model = $this->guard->getCredentials($request);

        Assert::assertEquals('tok_3dasdfadfs', $model->getToken());
        Assert::assertEquals('refresh_token', $model->getProvider());
    }

    public function testLoginRequestWithEmail()
    {
        $cred = ['email' => 'noah@gmail.com', 'password' => 'pass_word'];
        $request = Request::create('/login_check', 'POST',$cred);

        $model = $this->guard->getCredentials($request);

        Assert::assertEquals('noah@gmail.com', $model->getEmail());
        Assert::assertEquals('pass_word', $model->getPassword());
    }

    public function testStatelessLogin()
    {
        $request = Request::create('/users', 'PATCH',['moo' => 'blue']);
        $request->headers->set('Authorization', 'Bearer auth_token');

        $model = $this->guard->getCredentials($request);
        Assert::assertEquals('auth_token', $model->getToken());
        Assert::assertEquals('jwt', $model->getProvider());
    }

    public function testRandomPostNonLogin()
    {
        $request = Request::create('/users', 'PATCH',['moo' => 'blue']);

        $model = $this->guard->getCredentials($request);
        Assert::assertEmpty($model);
    }

    public function testEmailWithoutPassword()
    {
        $cred = [ 'password' => 'pass_word'];

        $request = Request::create('/login_check', 'POST', $cred);

        $model = $this->guard->getCredentials($request);
        Assert::assertEmpty($model);
    }

    public function testGetUserWithProvider()
    {
        $user = new User();
        $userProvider = \Mockery::mock(UserProviderInterface::class);
        $userProvider->shouldReceive('loadUserByUsername')->once()->with('token')->andReturn($user);

        $this->userProviderFactory
                ->shouldReceive('getClient')
                ->with('facebook')
                ->once()
                ->andReturn($userProvider);

        $userReturned = $this->guard
            ->getUser(
                new CredentialTokenModel('facebook', 'token'),
                \Mockery::mock(UserProviderInterface::class)
            );

        Assert::assertEquals($user,$userReturned);
    }

    public function testGetUserWithNoProvider()
    {
        $this->expectException(UsernameNotFoundException::class);
        $this->expectExceptionCode(ProgrammerException::NO_TOKEN_PROVIDER_IMPLEMENTED);

        $this->userProviderFactory
            ->shouldReceive('getClient')
            ->with('facebook')
            ->once()
            ->andThrow(new NotImplementedException('none'));

        $this->guard
            ->getUser(
                new CredentialTokenModel('facebook', 'token'),
                \Mockery::mock(UserProviderInterface::class)
            );

    }

    public function testCheckCredentialTokenResponse()
    {
       $cred = new CredentialTokenModel('facebook', 'token');

       Assert::assertTrue($this->guard->checkCredentials($cred, new User()));
    }

    public function testCheckCredentialWithValidPassword()
    {
        $user = new User();
        $user->setPassword('encoded_password');

        $encoder = \Mockery::mock(PasswordEncoderInterface::class);
        $encoder->shouldReceive('isPasswordValid')->with('encoded_password', 'password', null)->andReturn(true);

        $cred = new CredentialEmailModel('no@gmail.com', 'password');

        $this->encoderFactory
            ->shouldReceive('getEncoder')
            ->with($user)
            ->andReturn($encoder);

        Assert::assertTrue($this->guard->checkCredentials($cred, $user));
    }

    public function testCheckCredentialWithInValidPassword()
    {
        $user = new User();
        $user->setPassword('encoded_password');

        $encoder = \Mockery::mock(PasswordEncoderInterface::class);
        $encoder->shouldReceive('isPasswordValid')->with('encoded_password', 'password', null)->andReturn(false);

        $cred = new CredentialEmailModel('no@gmail.com', 'password');

        $this->encoderFactory
            ->shouldReceive('getEncoder')
            ->with($user)
            ->andReturn($encoder);

        Assert::assertFalse($this->guard->checkCredentials($cred, $user));

    }

    public function testOnAuthSuccessWithLoginResponse()
    {
        $user = new User();
        $token = new PostAuthenticationGuardToken($user, 'main', ['ROLE_USER']);
        $cred = ['email' => 'noah@gmail.com', 'password' => 'pass_word'];
        $request = Request::create('/login_check', 'POST',$cred);
        $response = new Response();
        $this->authResponseService->shouldReceive('createAuthResponse')->with($user)->once()->andReturn($response);
        $this->dispatcher->shouldReceive('dispatch')
            ->with(SimpleGuard::AUTH_LOGIN_SUCCESS,
                \Mockery::on(function (UserEvent $event) use($user) {
                    Assert::assertEquals($user, $event->getUser());
                    return true;
                })
            )->once();
        $actualResponse = $this->guard->onAuthenticationSuccess($request, $token, 'main');

        Assert::assertEquals($response, $actualResponse);
    }

    public function testOnAuthSuccessWithRegularRequest()
    {
        $user = new User();
        $token = new PostAuthenticationGuardToken($user, 'main', ['ROLE_USER']);
        $request = Request::create('/users', 'GET',[]);

        Assert::assertNull($this->guard->onAuthenticationSuccess($request, $token, 'main'));
    }

    public function testOnAuthFailureWithHTML()
    {
        $exception =  new AuthenticationException('failure');
        $request = Request::create('/users', 'GET',[]);
        $request->headers->set('Content-Type', 'html/text');
        $this->twig->shouldReceive('render')->with('TwigBundle:Exception:error403.html.twig')->andReturn('hello');

        $this->dispatcher->shouldReceive('dispatch')
            ->with(SimpleGuard::AUTH_FAILED_EVENT,
            \Mockery::on(function (AuthFailedEvent $event) use($request, $exception) {
                Assert::assertEquals($exception, $event->getException());
                Assert::assertEquals($request, $event->getRequest());
                return true;
            })
            )->once();
        $response = $this->guard->onAuthenticationFailure($request,$exception);

        Assert::assertEquals(Response::HTTP_FORBIDDEN, $response->getStatusCode());
        Assert::assertEquals('hello', $response->getContent());

    }

    public function testOnAuthFailureWithJSON()
    {
        $exception = new AuthenticationException('failure');
        $request = Request::create('/users', 'GET',[]);
        $request->headers->set('Content-Type', 'application/json');

        $this->dispatcher->shouldReceive('dispatch')
            ->with(SimpleGuard::AUTH_FAILED_EVENT,
                \Mockery::on(function (AuthFailedEvent $event) use($request, $exception) {
                    Assert::assertEquals($exception, $event->getException());
                    Assert::assertEquals($request, $event->getRequest());
                    return true;
                })
            )->once();
        $response = $this->guard->onAuthenticationFailure($request,$exception );

        Assert::assertEquals(Response::HTTP_FORBIDDEN, $response->getStatusCode());
        Assert::assertEquals('Authentication Failed', $response->getContent());
    }

    public function testStartWithHTML()
    {
        $request = Request::create('/users', 'GET',[]);
        $request->headers->set('Content-Type', 'html/text');
        $this->twig->shouldReceive('render')->with('TwigBundle:Exception:error403.html.twig')->andReturn('hello');

        $response = $this->guard->start($request, new AuthenticationException('failure'));

        Assert::assertEquals(Response::HTTP_UNAUTHORIZED, $response->getStatusCode());
        Assert::assertEquals('hello', $response->getContent());

    }

    public function testStartWithJSON()
    {
        $request = Request::create('/users', 'GET',[]);
        $request->headers->set('Content-Type', 'application/json');

        $response = $this->guard->start($request, new AuthenticationException('failure'));

        Assert::assertEquals(Response::HTTP_UNAUTHORIZED, $response->getStatusCode());
        Assert::assertEquals('Authentication Required', $response->getContent());
    }

    public function testRememberMeReturnFalse()
    {
        Assert::assertFalse($this->guard->supportsRememberMe());
    }
}
