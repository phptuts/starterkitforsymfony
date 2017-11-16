<?php

namespace StarterKit\StartBundle\Tests\Controller;

use PHPUnit\Framework\Assert;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class ForgetPasswordWorkFlowTest extends BaseApiTestCase
{
    /**
     * The email forget password test
     * @var string
     */
    const TEST_EMAIL = 'forget_password_end_2_end@gmail.com';

    /**
     * Test that a user can request to get a forget password email with token
     */
    public function testForgetPasswordSendEmail()
    {
        $client = $this->makeClient();
        $response = $this->makeJsonRequest(
            $client,
            Request::METHOD_POST,
            '/api/users/forget-password',
            ['email' => 'forget_password_end_2_end@gmail.com']
        );

        Assert::assertEquals(Response::HTTP_NO_CONTENT, $response->getStatusCode());

        return $this->userRepository
                    ->findUserByEmail(self::TEST_EMAIL)
                    ->getForgetPasswordToken();
    }

    public function testForgetPasswordBadEmail()
    {
        $client = $this->makeClient();
        $response = $this->makeJsonRequest(
            $client,
            Request::METHOD_POST,
            '/api/users/forget-password',
            ['email' => 'forget_password_end_2']
        );

        Assert::assertEquals(Response::HTTP_BAD_REQUEST, $response->getStatusCode());
        $json = json_decode($response->getContent(), true);
        Assert::assertNotEmpty($json['data']['children']['email']['errors'][0]);
    }

    /**
     * Test that an invalid plain password will fail validation
     *
     * @depends testForgetPasswordSendEmail
     * @param $forgetPasswordToken
     * @return string
     */
    public function testResetPasswordInvalidPasswordTestValidation($forgetPasswordToken)
    {
        $client = $this->makeClient();
        $response = $this->makeJsonRequest(
            $client,
            Request::METHOD_PATCH,
            '/api/users/reset-password/' . $forgetPasswordToken,
            ['plainPassword' => '']
        );

        Assert::assertEquals(Response::HTTP_BAD_REQUEST, $response->getStatusCode());

        return $forgetPasswordToken;
    }

    public function testResetPasswordInvalidToken()
    {
        $client = $this->makeClient();
        $response = $this->makeJsonRequest(
            $client,
            Request::METHOD_PATCH,
            '/api/users/reset-password/' . 'bad_password_token',
            ['plainPassword' => '']
        );

        Assert::assertEquals(Response::HTTP_NOT_FOUND, $response->getStatusCode());

    }

    /**
     * Tests that a user can rest their password with a valid reset token
     * @depends testResetPasswordInvalidPasswordTestValidation
     * @param string $forgetPasswordToken
     */
    public function testResetPassword($forgetPasswordToken)
    {
        $client = $this->makeClient();
        $response = $this->makeJsonRequest(
            $client,
            Request::METHOD_PATCH,
            '/api/users/reset-password/' . $forgetPasswordToken,
            ['plainPassword' => 'moo_moo']
        );

        Assert::assertEquals(Response::HTTP_NO_CONTENT, $response->getStatusCode());

    }

    /**
     * Tests that a user can login with the new passowrd
     * @depends testResetPassword
     */
    public function testLoginWithNewPassword()
    {
        $client = $this->makeClient();
        $response = $this->makeJsonRequest(
            $client,
            Request::METHOD_POST,
            '/login_check',
            ['email' => self::TEST_EMAIL, 'password' => 'moo_moo']
        );

        $this->assertCredentialsResponse($response, $client, self::TEST_EMAIL);
    }
}