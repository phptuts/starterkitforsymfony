<?php
namespace Test\CoreBundle\Security\Provider;

use CoreBundle\Entity\User;
use CoreBundle\Exception\ProgrammerException;
use CoreBundle\Factory\GoogleClientFactory;
use CoreBundle\Repository\UserRepository;
use CoreBundle\Security\Provider\GoogleProvider;
use CoreBundle\Service\User\RegisterService;
use Mockery\Mock;
use PHPUnit\Framework\Assert;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Symfony\Component\Security\Core\Exception\UsernameNotFoundException;
use Symfony\Component\Security\Core\User\UserInterface;
use Tests\BaseTestCase;

class GoogleProviderTest extends BaseTestCase
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
     * @var \Google_Client|Mock
     */
    protected $googleClient;

    /**
     * @var GoogleProvider
     */
    protected $googleProvider;

    protected function setUp()
    {
        parent::setUp();
        $this->userRepository = \Mockery::mock(UserRepository::class);
        $this->registerService = \Mockery::mock(RegisterService::class);
        $googleClientFactory = \Mockery::mock(GoogleClientFactory::class);
        $this->googleClient = \Mockery::mock(\Google_Client::class);
        $googleClientFactory->shouldReceive('getGoogleClient')->once()->andReturn($this->googleClient);

        $this->googleProvider = new GoogleProvider($this->userRepository, $this->registerService, $googleClientFactory);
    }

    public function testServiceId()
    {
        Assert::assertInstanceOf(GoogleProvider::class, $this->getContainer()->get('startsymfony.core.security.google_provider'));
    }

    public function testNewUser()
    {
        $this->userRepository->shouldReceive('findUserByEmail')->with('blue@gmail.com')->once()->andReturnNull();
        $token = 'asdfasdfasdfasdf';
        $this->googleClient->shouldReceive('verifyIdToken')->once()->with($token)->andReturn(['email' => 'blue@gmail.com']);
        $this->registerService->shouldReceive('registerUser')->once()->with(\Mockery::type(User::class));
        $user = $this->googleProvider->loadUserByUsername($token);

        Assert::assertNotEmpty($user->getPlainPassword());
        Assert::assertNotEmpty($user->getEmail());
    }

    public function testUserFound()
    {
        $user = new User();
        $this->userRepository->shouldReceive('findUserByEmail')->with('blue@gmail.com')->once()->andReturn($user);
        $token = 'asdfasdfasdfasdf';
        $this->googleClient->shouldReceive('verifyIdToken')->once()->with($token)->andReturn(['email' => 'blue@gmail.com']);
        $this->registerService->shouldReceive('registerUser')->never()->withAnyArgs();
        $returnedUser = $this->googleProvider->loadUserByUsername($token);

        Assert::assertEquals($user, $returnedUser);
    }

    public function testLogicExceptionIsThrownTransformedToUsernameNotFoundException()
    {
        $token = 'asdfasdfasdfasdf';
        $this->googleClient->shouldReceive('verifyIdToken')->once()->with($token)->andThrow(new \LogicException('bad logic'));

        $this->expectException(UsernameNotFoundException::class);
        $this->expectExceptionCode(ProgrammerException::GOOGLE_USER_PROVIDER_LOGIC_EXCEPTION);
        $this->expectExceptionMessage("Google AuthToken Did Not validate, ERROR MESSAGE bad logic");

        $this->googleProvider->loadUserByUsername($token);
    }

    public function testExceptionIsThrownTransformedToUsernameNotFoundException()
    {
        $token = 'asdfasdfasdfasdf';
        $this->googleClient->shouldReceive('verifyIdToken')->once()->with($token)->andThrow(new \Exception('bad logic'));

        $this->expectException(UsernameNotFoundException::class);
        $this->expectExceptionCode(ProgrammerException::GOOGLE_USER_PROVIDER_EXCEPTION);
        $this->expectExceptionMessage("Google AuthToken Did Not validate, ERROR MESSAGE bad logic");

        $this->googleProvider->loadUserByUsername($token);

    }

    public function testRefreshUserWithUnsupportedUserThrowsException()
    {
        $this->expectException(UnsupportedUserException::class);
        $this->googleProvider->refreshUser(new BlahUser());
    }

    public function testRefreshUserValidUserReturns()
    {
        $user = new User();
        $user->setEmail('bluemoo@gmail.com');
        $this->userRepository->shouldReceive('findUserByEmail')->with('bluemoo@gmail.com')->once()->andReturn($user);

        /** @var User $userRefreshed */
        $userRefreshed =  $this->googleProvider->refreshUser($user);
        Assert::assertEquals($user->getEmail(), $userRefreshed->getEmail());

    }

    public function testSupportedClass()
    {
        Assert::assertTrue($this->googleProvider->supportsClass(User::class));
        Assert::assertFalse($this->googleProvider->supportsClass(BlahUser::class));
    }
}

class BlahUser implements UserInterface {
    /**
     * @inheritDoc
     */
    public function getRoles()
    {
        // TODO: Implement getRoles() method.
    }

    /**
     * @inheritDoc
     */
    public function getPassword()
    {
        // TODO: Implement getPassword() method.
    }

    /**
     * @inheritDoc
     */
    public function getSalt()
    {
        // TODO: Implement getSalt() method.
    }

    /**
     * @inheritDoc
     */
    public function getUsername()
    {
        // TODO: Implement getUsername() method.
    }

    /**
     * @inheritDoc
     */
    public function eraseCredentials()
    {
        // TODO: Implement eraseCredentials() method.
    }


}