<?php

namespace Tests\ApiBundle\Controller;

use CoreBundle\Entity\User;
use Liip\FunctionalTestBundle\Test\WebTestCase;
use PHPUnit\Framework\Assert;
use Symfony\Bundle\FrameworkBundle\Client;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class BaseApiTestCase extends WebTestCase
{
    use RequestTrait;

    /**
     * Gets an auth token for logging in stateless through the api
     *
     * @param User $user
     * @return string
     */
    protected function getAuthToken($user)
    {
        return $this->getContainer()
            ->get('startsymfony.core.jws_service')
            ->createAuthTokenModel($user)
            ->getToken();
    }

    /**
     * Asserts that the credentials response is valid
     *
     * @param Response $response
     * @param string $email
     * @param Client $client
     */
    public function assertCredentialsResponse(Response $response, Client $client, $email)
    {
        $json = $this->getJsonResponse($response);

        // Asserting meta data in response
        Assert::assertEquals('credentials',$json['meta']['type']);
        Assert::assertFalse($json['meta']['paginated']);

        // Asserting tokens in credential response
        $tokenModel = $json['data']['tokenModel'];
        $refreshTokenModel = $json['data']['refreshTokenModel'];

        $this->assertJWSToken($tokenModel['token'], $email, $tokenModel['expirationTimeStamp']);
        $this->assertRefreshToken($refreshTokenModel['token'], $email, $refreshTokenModel['expirationTimeStamp']);

        $user = $json['data']['user'];

        Assert::assertArrayHasKey('id', $user);
        Assert::assertArrayHasKey('displayName', $user);
        Assert::assertEquals($email, $user['email']);

        $response = $this->makeJsonRequest(
            $client,
            Request::METHOD_GET,
            '/api/users/' . $user['id'],
            [],
            $tokenModel['token']
        );

        // Asserting that the user can view itself
        Assert::assertEquals(Response::HTTP_OK, $response->getStatusCode());

        $userEntity = $this->getContainer()->get('startsymfony.core.repository.user_repository')->find($user['id']);

        Assert::assertInstanceOf(User::class, $userEntity);

    }

    /**
     * Asserts that the jws token and the email match and that the jws token is valid.
     *
     * @param string $token
     * @param string $email
     * @param int $exp
     */
    public function assertJWSToken($token, $email, $exp)
    {
        $jwsService = $this->getContainer()->get('startsymfony.core.jws_service');
        $userRepo = $this->getContainer()->get('startsymfony.core.repository.user_repository');

        // Asserts that the token is valid
        Assert::assertTrue($jwsService->isValid($token));

        // Checks the token's payload
        $tokenPayload = $jwsService->getPayload($token);
        Assert::assertNotEmpty($tokenPayload['user_id']);
        Assert::assertNotEmpty($tokenPayload['exp']);
        Assert::assertNotEmpty($tokenPayload['iat']);

        // Asserts that the token's user id match the email of user who got it
        $user = $userRepo->findUserByEmail($email);
        Assert::assertEquals($user->getId(), $tokenPayload['user_id']);

        $ttlForTokenInSeconds = $this->getContainer()->getParameter('jws_ttl');
        $lessThanExpirationTimeStamp = (new \DateTime())->modify('+' . ($ttlForTokenInSeconds - 500) .  ' seconds')->getTimestamp();
        $greaterThanExpirationTimeStamp = (new \DateTime())->modify('+' . ($ttlForTokenInSeconds + 500) .  ' seconds')->getTimestamp();

        // Asserts that the expiration timestamp is with 500 seconds
        Assert::assertTrue($lessThanExpirationTimeStamp < $tokenPayload['exp']);
        Assert::assertTrue($greaterThanExpirationTimeStamp > $tokenPayload['exp']);
        Assert::assertEquals($tokenPayload['exp'], $exp);

    }

    /**
     * Asserts that the refresh token is valid
     *
     * @param string $token
     * @param string $email
     * @param integer $exp
     */
    public function assertRefreshToken($token, $email, $exp)
    {
        $refreshTokenRepo = $this->getContainer()->get('startsymfony.core.repository.refreshtoken_repository');

        $refreshToken = $refreshTokenRepo->getValidRefreshToken($token);

        Assert::assertEquals($refreshToken->getUser()->getEmail(), $email);

        $ttlForTokenInSeconds = $this->getContainer()->getParameter('refresh_token_ttl');
        $lessThanExpirationTimeStamp = (new \DateTime())->modify('+' . ($ttlForTokenInSeconds - 1500) .  ' seconds')->getTimestamp();
        $greaterThanExpirationTimeStamp = (new \DateTime())->modify('+' . ($ttlForTokenInSeconds + 1500) .  ' seconds')->getTimestamp();

        // Asserts that the expiration timestamp is with 500 seconds
        Assert::assertTrue($lessThanExpirationTimeStamp < $refreshToken->getExpires()->getTimestamp());
        Assert::assertTrue($greaterThanExpirationTimeStamp > $refreshToken->getExpires()->getTimestamp());
        Assert::assertEquals($refreshToken->getExpires()->getTimestamp(), $exp);
    }

    /**
     * Returns an array of email and facebook auth token
     * This is a crap way a of doing it fyi.
     *
     * @return array
     */
    protected function getFacebookAuthTokenAndEmail()
    {
        $url = 'https://graph.facebook.com/oauth/access_token?client_id=' . $this->getContainer()->getParameter('facebook_app_id')
            . '&client_secret=' . $this->getContainer()->getParameter('facebook_app_secret') . '&grant_type=client_credentials&redirect_uri=http://bigfootlocator.com';

        $data = json_decode(file_get_contents($url), true);

        $facebookClient = $this->getContainer()->get('startsymfony.core.facebook_client_factory')->getFacebookClient();

        $response = $facebookClient->get('/'. $this->getContainer()->getParameter('facebook_app_id') . '/accounts/test-users', $data['access_token']);

        $userAccessToken = $response->getDecodedBody()['data'][0]['access_token'];

        $response = $facebookClient->get('/me?fields=email', $userAccessToken);

        return ['email' => $response->getGraphUser()->getEmail(), 'token' => $userAccessToken];

    }
}