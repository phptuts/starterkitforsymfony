<?php

namespace Test\CoreBundle\Security\Provider;

use CoreBundle\Entity\User;
use CoreBundle\Exception\ProgrammerException;
use CoreBundle\Factory\FaceBookClientFactory;
use CoreBundle\Repository\UserRepository;
use CoreBundle\Security\Provider\FacebookProvider;
use CoreBundle\Service\User\RegisterService;
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
    private $userRepository;

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
        $this->userRepository = \Mockery::mock(UserRepository::class);
        $this->registerService = \Mockery::mock(RegisterService::class);
        $facebookClientFactory = \Mockery::mock(FaceBookClientFactory::class);
        $this->facebookClient = \Mockery::mock(Facebook::class);
        $facebookClientFactory->shouldReceive('getFacebookClient')->once()->andReturn($this->facebookClient);

        $this->facebookProvider = new FacebookProvider($facebookClientFactory, $this->userRepository, $this->registerService);
    }

    public function testUserNotFound()
    {
        $this->userRepository->shouldReceive('findUserByEmail')->with('blue@gmail.com')->once()->andReturnNull();
        $token = 'asdfasdfasdfasdf';

        $graphUser = \Mockery::mock(GraphUser::class);
        $graphUser->shouldReceive('getEmail')->andReturn('blue@gmail.com');

        $facebookResponse = \Mockery::mock(FacebookResponse::class);
        $facebookResponse->shouldReceive('getGraphUser')->andReturn($graphUser);


        $this->facebookClient->shouldReceive('get')->once()->with('/me?fields=email',$token)->andReturn($facebookResponse);
        $this->registerService->shouldReceive('registerUser')->once()->with(\Mockery::type(User::class), RegisterService::SOURCE_TYPE_FACEBOOK);
        $user = $this->facebookProvider->loadUserByUsername($token);

        Assert::assertNotEmpty($user->getPlainPassword());
        Assert::assertNotEmpty($user->getEmail());

    }

    public function testUserFound()
    {
        $user = new User();
        $user->setEmail('blue@gmail.com');
        $this->userRepository->shouldReceive('findUserByEmail')->with('blue@gmail.com')->once()->andReturn($user);
        $token = 'asdfasdfasdfasdf';

        $graphUser = \Mockery::mock(GraphUser::class);
        $graphUser->shouldReceive('getEmail')->andReturn('blue@gmail.com');

        $facebookResponse = \Mockery::mock(FacebookResponse::class);
        $facebookResponse->shouldReceive('getGraphUser')->andReturn($graphUser);


        $this->facebookClient->shouldReceive('get')->once()->with('/me?fields=email',$token)->andReturn($facebookResponse);
        $this->registerService->shouldReceive('registerUser')->never()->withAnyArgs();
        $returnedUser = $this->facebookProvider->loadUserByUsername($token);

        Assert::assertEquals($user, $returnedUser);
    }

    public function testFacebookResponseExceptionThrown()
    {
        $token = 'asdfasdfasdfasdf';

        $this->facebookClient->shouldReceive('get')->once()->with('/me?fields=email',$token)
            ->andThrow(\Mockery::mock(FacebookResponseException::class));

        $this->expectException(UsernameNotFoundException::class);
        $this->expectExceptionCode(ProgrammerException::FACEBOOK_RESPONSE_EXCEPTION_CODE);

        $this->facebookProvider->loadUserByUsername($token);

    }

    public function testFacebookSDKExceptionThrown()
    {
        $token = 'asdfasdfasdfasdf';

        $this->facebookClient->shouldReceive('get')->once()->with('/me?fields=email',$token)
            ->andThrow(\Mockery::mock(FacebookSDKException::class));

        $this->expectException(UsernameNotFoundException::class);
        $this->expectExceptionCode(ProgrammerException::FACEBOOK_SDK_EXCEPTION_CODE);

        $this->facebookProvider->loadUserByUsername($token);

    }

    public function testGeneralExceptionThorwn()
    {
        $token = 'asdfasdfasdfasdf';

        $this->facebookClient->shouldReceive('get')->once()->with('/me?fields=email',$token)
            ->andThrow(\Mockery::mock(\Exception::class));

        $this->expectException(UsernameNotFoundException::class);
        $this->expectExceptionCode(ProgrammerException::FACEBOOK_PROVIDER_EXCEPTION);

        $this->facebookProvider->loadUserByUsername($token);

    }

}