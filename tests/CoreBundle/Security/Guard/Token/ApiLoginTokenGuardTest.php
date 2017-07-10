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
     * Tests that the service definition is setup right
     */
    public function testServiceDefinition()
    {
        Assert::assertInstanceOf(ApiLoginTokenGuard::class, $this->getContainer()->get('startsymfony.core.security.api_login_token_guard'));
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

    public function testValidGetCredentials()
    {
        $request = Request::create('/api/login_check', 'POST', [],[],[],[], json_encode(['token' => 'token', 'type' => 'google']));
        $request->attributes->set('_route', 'api_login');

        $creds = $this->apiLoginTokenGuard->getCredentials($request);

        Assert::assertEquals('token', $creds['token']);
        Assert::assertEquals('google', $creds['type']);
    }


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

    public function dataProviderForGetCreds()
    {
        $request = Request::create('/api/login_check', 'POST', [],[],[],[], json_encode(['type' => 'google', 'token' => null]));
        $request->attributes->set('_route', 'api_login');


        $request2 = Request::create('/api/login_check', 'POST', [],[],[],[], json_encode(['email' => 'asdfasdf']));
        $request2->attributes->set('_route', 'api_login');


        $request3 = Request::create('/api/login_check', 'POST', [],[],[],[], json_encode(['password' => 'asdfasdf']));
        $request3->attributes->set('_route', 'api_login');

        return [
            [Request::create('/bad_end_point', 'POST')],
            [Request::create('/token_login_check', 'GET')],
            [$request],
            [$request2],
            [$request3]
        ];
    }
}