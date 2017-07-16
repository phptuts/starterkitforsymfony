<?php

namespace Test\CoreBundle\Security\Provider;

use CoreBundle\Entity\User;
use CoreBundle\Exception\ProgrammerException;
use CoreBundle\Factory\FaceBookClientFactory;
use CoreBundle\Repository\UserRepository;
use CoreBundle\Security\Provider\FacebookProvider;
use CoreBundle\Service\User\RegisterService;
use CoreBundle\Service\User\UserService;
use Facebook\Exceptions\FacebookResponseException;
use Facebook\Exceptions\FacebookSDKException;
use Facebook\Facebook;
use Facebook\FacebookResponse;
use Facebook\GraphNodes\GraphUser;
use Mockery\Mock;
use PHPUnit\Framework\Assert;
use Symfony\Component\Security\Core\Exception\UsernameNotFoundException;
use Tests\BaseTestCase;

class FacebookProviderTest extends BaseTestCase
{
    /**
     * @var UserRepository|Mock
     */
    private $userService;

    /**
     * @var RegisterService|Mock
     */
    private $registerService;

    /**
     * @var Facebook|Mock
     */
    protected $facebookClient;

    /**
     * @var FacebookProvider
     */
    protected $facebookProvider;


    protected function setUp()
    {
        parent::setUp();
        $this->userService = \Mockery::mock(UserRepository::class);
        $this->registerService = \Mockery::mock(RegisterService::class);
        $facebookClientFactory = \Mockery::mock(FaceBookClientFactory::class);
        $this->facebookClient = \Mockery::mock(Facebook::class);
        $facebookClientFactory->shouldReceive('getFacebookClient')->once()->andReturn($this->facebookClient);
        $this->userService = \Mockery::mock(UserService::class);

        $this->facebookProvider = new FacebookProvider($facebookClientFactory,  $this->registerService, $this->userService);
    }

    /**
     * Tests that if the user does not exists in our database that we create them via register service
     */
    public function testUserNotFound()
    {
        $this->userService->shouldReceive('findByFacebookUserId')->with('324323423')->once()->andReturnNull();
        $this->userService->shouldReceive('findUserByEmail')->with('blue@gmail.com')->once()->andReturnNull();

        $token = 'asdfasdfasdfasdf';

        $graphUser = \Mockery::mock(GraphUser::class);
        $graphUser->shouldReceive('getEmail')->andReturn('blue@gmail.com');
        $graphUser->shouldReceive('getId')->andReturn('324323423');

        $facebookResponse = \Mockery::mock(FacebookResponse::class);
        $facebookResponse->shouldReceive('getGraphUser')->andReturn($graphUser);


        $this->facebookClient->shouldReceive('get')->once()->with('/me?fields=email',$token)->andReturn($facebookResponse);
        $this->registerService
            ->shouldReceive('registerUser')
            ->once()->with(\Mockery::type(User::class), RegisterService::SOURCE_TYPE_FACEBOOK);

        /** @var User $user */
       $user = $this->facebookProvider->loadUserByUsername($token);

        Assert::assertNotEmpty($user->getPlainPassword());
        Assert::assertNotEmpty($user->getEmail());
        Assert::assertEquals('324323423', $user->getFacebookUserId());


    }

    /**
     * Tests that if a user's email is found that we save the user with facebook user id and return it
     */
    public function testUserEmailFound()
    {
        $user = new User();
        $user->setEmail('blue@gmail.com');
        $this->userService->shouldReceive('findByFacebookUserId')->with('324323423')->once()->andReturnNull();
        $this->userService->shouldReceive('findUserByEmail')->with('blue@gmail.com')->once()->andReturn($user);
        $token = 'asdfasdfasdfasdf';

        $graphUser = \Mockery::mock(GraphUser::class);
        $graphUser->shouldReceive('getEmail')->andReturn('blue@gmail.com');
        $graphUser->shouldReceive('getId')->andReturn('324323423');

        $facebookResponse = \Mockery::mock(FacebookResponse::class);
        $facebookResponse->shouldReceive('getGraphUser')->andReturn($graphUser);


        $this->facebookClient->shouldReceive('get')->once()->with('/me?fields=email',$token)->andReturn($facebookResponse);
        $this->registerService->shouldReceive('registerUser')->never()->withAnyArgs();
        $this->userService->shouldReceive('save')->with(\Mockery::type(User::class));

        /** @var User $returnedUser */
        $returnedUser = $this->facebookProvider->loadUserByUsername($token);

        Assert::assertEquals($user, $returnedUser);
        Assert::assertEquals('324323423', $returnedUser->getFacebookUserId());

    }

    /**
     * Testing that if the facebook token is found that we just return that user
     */
    public function testFacebookUserIdFound()
    {
        $token = 'adfasdfasdfasd';
        $user = new User();
        $user->setEmail('blue@gmail.com');
        $user->setFacebookUserId('324323423');
        $this->userService->shouldReceive('findByFacebookUserId')->with('324323423')->once()->andReturn($user);

        $graphUser = \Mockery::mock(GraphUser::class);
        $graphUser->shouldReceive('getEmail')->andReturn('blue@gmail.com');
        $graphUser->shouldReceive('getId')->andReturn('324323423');

        $facebookResponse = \Mockery::mock(FacebookResponse::class);
        $facebookResponse->shouldReceive('getGraphUser')->andReturn($graphUser);


        $this->facebookClient->shouldReceive('get')->once()->with('/me?fields=email',$token)->andReturn($facebookResponse);
        $this->registerService->shouldReceive('registerUser')->never()->withAnyArgs();
        $this->userService->shouldReceive('save')->with(\Mockery::type(User::class));


        /** @var User $returnedUser */
        $returnedUser = $this->facebookProvider->loadUserByUsername($token);

        Assert::assertEquals($user, $returnedUser);
    }

    /**
     * Test that if we get a bad response from facebook we throw a UsernameNotFoundException
     */
    public function testFacebookResponseExceptionThrown()
    {
        $token = 'asdfasdfasdfasdf';

        $this->facebookClient->shouldReceive('get')->once()->with('/me?fields=email',$token)
            ->andThrow(\Mockery::mock(FacebookResponseException::class));

        $this->expectException(UsernameNotFoundException::class);
        $this->expectExceptionCode(ProgrammerException::FACEBOOK_RESPONSE_EXCEPTION_CODE);

        $this->facebookProvider->loadUserByUsername($token);

    }

    /**
     * Test that if we get a sdk error  we throw a UsernameNotFoundException
     */
    public function testFacebookSDKExceptionThrown()
    {
        $token = 'asdfasdfasdfasdf';

        $this->facebookClient->shouldReceive('get')->once()->with('/me?fields=email',$token)
            ->andThrow(\Mockery::mock(FacebookSDKException::class));

        $this->expectException(UsernameNotFoundException::class);
        $this->expectExceptionCode(ProgrammerException::FACEBOOK_SDK_EXCEPTION_CODE);

        $this->facebookProvider->loadUserByUsername($token);

    }

    /**
     * Test that if we get any exception  we throw a UsernameNotFoundException
     */
    public function testGeneralExceptionThrown()
    {
        $token = 'asdfasdfasdfasdf';

        $this->facebookClient->shouldReceive('get')->once()->with('/me?fields=email',$token)
            ->andThrow(\Mockery::mock(\Exception::class));

        $this->expectException(UsernameNotFoundException::class);
        $this->expectExceptionCode(ProgrammerException::FACEBOOK_PROVIDER_EXCEPTION);

        $this->facebookProvider->loadUserByUsername($token);

    }

}