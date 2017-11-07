<?php

namespace StarterKit\StartBundle\Tests\Controller;

use PHPUnit\Framework\Assert;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class AuthTest extends BaseApiTestCase
{
    /**
     * The auth test email
     * @var string
     */
    const TEST_EMAIL = 'glaserpower+register_test@gmail.com';

    /**
     * Tests that registration validation works
     */
    public function testRegisterValidation()
    {
        $client = $this->makeClient();
        $response = $this->makeJsonRequest($client, Request::METHOD_POST, '/api/users', []);
        $json = $this->getJsonResponse($response);

        Assert::assertEquals(Response::HTTP_BAD_REQUEST, $response->getStatusCode());

        Assert::assertNotEmpty($json['data']['children']['email']['errors']);
        Assert::assertNotEmpty($json['data']['children']['plainPassword']['errors']);
        Assert::assertEquals('formErrors',$json['meta']['type']);
        Assert::assertFalse($json['meta']['paginated']);
    }

    /**
     * Tests that registration works and that the credential response is valid
     */
    public function testRegister()
    {
        $client = $this->makeClient();
        $response = $this->makeJsonRequest(
            $client,
            Request::METHOD_POST,
            '/api/users',
            ['email' => self::TEST_EMAIL, 'plainPassword' => 'password']
        );

        $this->assertCredentialsResponse($response, $client, self::TEST_EMAIL);

        return $this->getJsonResponse($response)['data']['refreshTokenModel']['token'];


    }

    /**
     * Test the new user can login with api
     *
     * @depends testRegister
     */
    public function testApiLoginEmailAndPassword()
    {
        $client = $this->makeClient();
        $response = $this->makeJsonRequest(
            $client,
            Request::METHOD_POST,
            '/login_check',
            ['email' => self::TEST_EMAIL, 'password' => 'password']
        );

        $this->assertCredentialsResponse($response, $client, self::TEST_EMAIL);
    }

    /**
     * Tests that invalid credential returns a 403 response.
     */
    public function testApiInvalidCredentials()
    {
        $client = $this->makeClient();
        $response = $this->makeJsonRequest(
            $client,
            Request::METHOD_POST,
            '/login_check',
            ['email' => 'adsfasdfasdfa', 'password' => 'password']
        );

        Assert::assertEquals(Response::HTTP_FORBIDDEN, $response->getStatusCode());
    }

    /**
     * Test that a user can use a refresh token to login through the api
     *
     * @depends testRegister
     * @param $refreshToken
     */
    public function testRefreshTokenLogin($refreshToken)
    {
        $client = $this->makeClient();
        $response = $this->makeJsonRequest(
            $client,
            Request::METHOD_POST,
            '/login_check',
            ['type' => 'refresh_token', 'token' => $refreshToken]
        );

        $this->assertCredentialsResponse($response, $client, self::TEST_EMAIL);
    }

//    /**
//     * This is excluded from travis ci because it involves a secret
//     *
//     * @group exclude_travis
//     * Tests that a facebook user can login
//     */
//    public function testFacebookLogin()
//    {
//        $facebookAuthToken = $this->getFacebookAuthTokenAndEmail();
//
//        $client = $this->makeClient();
//        $response = $this->makeJsonRequest(
//            $client,
//            Request::METHOD_POST,
//            '/login_check',
//            ['type' => 'facebook', 'token' => $facebookAuthToken['token']]
//        );
//
//        $this->assertCredentialsResponse($response, $client, $facebookAuthToken['email']);
//
//        $user = $this->userRepository->findUserByEmail($facebookAuthToken['email']);
//
//        Assert::assertInstanceOf(User::class, $user);
//        // Tests that the facebook user id is not empty
//        Assert::assertNotEmpty($user->getFacebookUserId());
//
//    }
}