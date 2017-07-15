<?php


namespace Test\CoreBundle\Security\Guard\Token;

use CoreBundle\Entity\User;
use CoreBundle\Factory\UserProviderFactory;
use CoreBundle\Security\Guard\Token\ApiLoginTokenGuard;
use CoreBundle\Service\Credential\CredentialResponseBuilderService;
use Mockery\Mock;
use PHPUnit\Framework\Assert;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Tests\BaseTestCase;

class ApiLoginTokenGuardTest extends BaseTestCase
{
    /**
     * @var ApiLoginTokenGuard|Mock
     */
    protected $apiLoginTokenGuard;

    /**
     * @var UserProviderFactory|Mock
     */
    protected $userProviderFactory;

    /**
     * @var CredentialResponseBuilderService|Mock
     */
    protected $credentialResponseBuilderService;



    public function setUp()
    {
        parent::setUp();
        $this->userProviderFactory = \Mockery::mock(UserProviderFactory::class);
        $this->credentialResponseBuilderService = \Mockery::mock(CredentialResponseBuilderService::class);
        $this->apiLoginTokenGuard = new ApiLoginTokenGuard($this->userProviderFactory, $this->credentialResponseBuilderService);
    }


    /**
     * This tests a bunch of invalid requests
     * @param Request $request
     * @dataProvider dataProviderForGetCreds
     */
    public function testGetCredentialsOnBadRequests(Request $request)
    {
        Assert::assertNull($this->apiLoginTokenGuard->getCredentials($request));
    }

    /**
     * Tests that a valid request is passed through
     * This is used for authenticating refresh tokens / facebook / google / etc
     * They pass in a token and a type
     */
    public function testValidGetCredentials()
    {
        $request = Request::create('/api/login_check', 'POST', [],[],[],[], json_encode(['token' => 'token', 'type' => 'google']));
        $request->attributes->set('_route', 'api_login');

        $creds = $this->apiLoginTokenGuard->getCredentials($request);

        Assert::assertEquals('token', $creds['token']);
        Assert::assertEquals('google', $creds['type']);
    }

    /**
     * This tests that a credential response is returned when token auth works
     */
    public function testOnAuthenticationSuccess()
    {
        $user = new User();
        $response = \Mockery::mock(JsonResponse::class);
        $token = \Mockery::mock(TokenInterface::class);
        $token->shouldReceive('getUser')->andReturn($user);

        $this->credentialResponseBuilderService->shouldReceive('createCredentialResponse')->with($user)->andReturn($response);
        $request = Request::create('/api/login_check', 'POST', [],[],[],[], json_encode(['type' => 'google', 'token' => null]));

        $responseReturned = $this->apiLoginTokenGuard->onAuthenticationSuccess($request, $token, 'main');

        Assert::assertEquals($response, $responseReturned);
    }

    /**
     *
     * Provides a bunch of invalid request to make sure they all return null
     * @return array
     */
    public function dataProviderForGetCreds()
    {
        $request = Request::create('/api/login_check', 'POST', [],[],[],[], json_encode(['type' => 'google', 'token' => null]));
        $request2 = Request::create('/api/login_check', 'POST', [],[],[],[], json_encode(['email' => 'asdfasdf']));
        $request3 = Request::create('/api/login_check', 'POST', [],[],[],[], json_encode(['password' => 'asdfasdf']));

        return [
            [Request::create('/bad_end_point', 'POST')],
            [Request::create('/token_login_check', 'GET')],
            [$request],
            [$request2],
            [$request3]
        ];
    }
}