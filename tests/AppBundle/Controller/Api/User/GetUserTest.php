<?php


namespace Tests\AppBundle\Controller\Api\User;

use AppBundle\Entity\User;
use AppBundle\Repository\UserRepository;
use PHPUnit\Framework\Assert;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Tests\AppBundle\Controller\Api\BaseApiTestCase;

class GetUserTest extends BaseApiTestCase
{
    /**
     * @var string
     */
    const TEST_EMAIL = 'admin_user@gmail.com';


    /**
     * Get's the user and tests that the response is solid
     */
    public function testGetUser()
    {
        $client = $this->makeClient();
        $user = $this->userRepository->findUserByEmail(self::TEST_EMAIL);

        $authToken = $this->getAuthToken($user);

        $response = $this->makeJsonRequest(
            $client,
            Request::METHOD_GET,
            '/api/users/' . $user->getId(),
            [],
            $authToken
        );

        // Asserting that the user can view itself
        Assert::assertEquals(Response::HTTP_OK, $response->getStatusCode());
        $json = $this->getJsonResponse($response);

        Assert::assertArrayHasKey('paginated',$json['meta']);
        Assert::assertArrayHasKey('type',$json['meta']);

        Assert::assertArrayHasKey('id',$json['data']);
        Assert::assertArrayHasKey('email',$json['data']);
        Assert::assertArrayHasKey('displayName',$json['data']);
    }

    /**
     * Tests get users api for meta data
     *
     * @dataProvider dataProvidersForGetUser
     * @param $queryString
     */
    public function testGetUsers($queryString)
    {
        $client = $this->makeClient();
        $user = $this->userRepository->findUserByEmail(self::TEST_EMAIL);

        $authToken = $this->getAuthToken($user);


        $response = $this->makeJsonRequest(
            $client,
            Request::METHOD_GET,
            '/api/users'. $queryString,
            [],
            $authToken
        );

        $this->assertGetUserResponse($response);
    }

    /**
     * Tests that meta fields are set and that data field exists
     * @param Response $response
     */
    private function assertGetUserResponse(Response $response)
    {
        Assert::assertEquals(Response::HTTP_OK, $response->getStatusCode());
        $json = $this->getJsonResponse($response);
        $meta = $json['meta'];

        Assert::assertArrayHasKey('type', $meta);
        Assert::assertArrayHasKey('paginated', $meta);
        Assert::assertArrayHasKey('total', $meta);
        Assert::assertArrayHasKey('page', $meta);
        Assert::assertArrayHasKey('pageSize', $meta);
        Assert::assertArrayHasKey('totalPages', $meta);

        Assert::assertArrayHasKey('data', $json);

    }

    /**
     * Some basic query string to smoke test get users
     * @return array
     */
    public function dataProvidersForGetUser()
    {
        return [
            ['?q=don'],
            ['?p=3'],
            ['?q=don&p=3']
        ];
    }

}