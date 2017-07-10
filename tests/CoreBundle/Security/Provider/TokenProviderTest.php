<?php


namespace Test\CoreBundle\Security\Provider;

use CoreBundle\Entity\User;
use CoreBundle\Repository\UserRepository;
use CoreBundle\Security\Provider\TokenProvider;
use CoreBundle\Service\Credential\JWSService;
use Mockery\Mock;
use PHPUnit\Framework\Assert;
use Symfony\Component\Security\Core\Exception\UsernameNotFoundException;
use Tests\BaseTestCase;

class TokenProviderTest extends BaseTestCase
{
    /**
     * @var TokenProvider|Mock
     */
    protected $tokenProvider;

    /**
     * @var JWSService|Mock
     */
    protected $jwsService;

    /**
     * @var UserRepository|Mock
     */
    protected $userRepo;

    public function setUp()
    {
        parent::setUp();

        $this->jwsService = \Mockery::mock(JWSService::class);
        $this->userRepo = \Mockery::mock(UserRepository::class);
        $this->tokenProvider = new TokenProvider($this->userRepo, $this->jwsService);
    }

    public function testServiceName()
    {
        Assert::assertInstanceOf(TokenProvider::class, $this->getContainer()->get('startsymfony.core.security.token_provider'));
    }

    public function testValidToken()
    {
        $user = new User();
        $this->jwsService->shouldReceive('isValid')->with('token')->andReturn(true);
        $this->jwsService->shouldReceive('getPayLoad')->with('token')->andReturn(['user_id' => 33]);
        $this->userRepo->shouldReceive('find')->with('33')->andReturn($user);
        $userFound = $this->tokenProvider->loadUserByUsername('token');

        Assert::assertEquals($user, $userFound);
    }

    public function testInvalidToken()
    {
        $this->expectException(UsernameNotFoundException::class);
        $this->jwsService->shouldReceive('isValid')->with('token')->andReturn(false);

        $this->tokenProvider->loadUserByUsername('token');
    }

    public function testTokenWithNoUserIdInPayload()
    {
        $this->expectException(UsernameNotFoundException::class);
        $this->jwsService->shouldReceive('isValid')->with('token')->andReturn(true);
        $this->jwsService->shouldReceive('getPayLoad')->with('token')->andReturn(['id' => 33]);

        $this->tokenProvider->loadUserByUsername('token');
    }

    public function testUserIdNotFoundInDatabase()
    {
        $this->expectException(UsernameNotFoundException::class);
        $this->jwsService->shouldReceive('isValid')->with('token')->andReturn(true);
        $this->jwsService->shouldReceive('getPayLoad')->with('token')->andReturn(['user_id' => 33]);

        $this->userRepo->shouldReceive('find')->with('33')->andReturnNull();
        $this->tokenProvider->loadUserByUsername('token');
    }
}