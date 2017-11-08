<?php

namespace StarterKit\StartBundle\Tests\Controller;

use PHPUnit\Framework\Assert;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class ChangePasswordTest extends BaseApiTestCase
{
    const TEST_EMAIL = 'change_password_end_2_end@gmail.com';

    /**
     * Tests that a user can change their password
     */
    public function testChangePasswordAction()
    {
        $user = $this->userRepository->findUserByEmail(self::TEST_EMAIL);
        $authToken = $this->getAuthToken($user);
        $client = $this->makeClient();
        $url = sprintf('/api/users/%s/password', $user->getId());
        $response = $this->makeJsonRequest(
            $client,
            Request::METHOD_PATCH,
            $url,
            ['newPassword' => 'moomoo', 'currentPassword' => 'password'],
            $authToken
        );

        Assert::assertEquals(Response::HTTP_NO_CONTENT, $response->getStatusCode());
    }

    /**
     * Tests that a user can login with their new password
     */
    public function testApiLoginWithNewPassword()
    {
        $client = $this->makeClient();
        $response = $this->makeJsonRequest(
            $client,
            Request::METHOD_POST,
            '/login_check',
            ['email' => self::TEST_EMAIL, 'password' => 'moomoo']
        );

        $this->assertCredentialsResponse($response, $client, self::TEST_EMAIL);
    }

    public function testBlankPassword()
    {
        $user = $this->userRepository->findUserByEmail(self::TEST_EMAIL);
        $authToken = $this->getAuthToken($user);
        $client = $this->makeClient();
        $url = sprintf('/api/users/%s/password', $user->getId());
        $response = $this->makeJsonRequest(
            $client,
            Request::METHOD_PATCH,
            $url,
            ['newPassword' => '', 'currentPassword' => 'password'],
            $authToken
        );

        Assert::assertEquals(Response::HTTP_BAD_REQUEST, $response->getStatusCode());
        $json = json_decode($response->getContent(), true);
        Assert::assertNotEmpty($json['data']['children']['newPassword']['errors'][0]);

    }
}