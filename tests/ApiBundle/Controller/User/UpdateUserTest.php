<?php

namespace Tests\ApiBundle\Controller\User;

use PHPUnit\Framework\Assert;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Tests\ApiBundle\Controller\BaseApiTestCase;

class UpdateUserTest extends BaseApiTestCase
{
    const TEST_EMAIL = 'update_user_e2e@email.com';

    /**
     * Tests that a user can update itself
     */
    public function testUpdateUser()
    {
        $client = $this->makeClient();
        $user = $this->getContainer()->get('startsymfony.core.user_service')->findUserByEmail(self::TEST_EMAIL);
        $authToken = $this->getAuthToken($user);
        $url = sprintf('/api/users/%s', $user->getId());
        $response = $this->makeJsonRequest(
            $client,
            Request::METHOD_PATCH,
            $url,
            ['email' => 'update_user_e2e@email.com', 'displayName' => 'blueMoo'],
            $authToken
        );

        Assert::assertEquals(Response::HTTP_OK, $response->getStatusCode());

        $json = $this->getJsonResponse($response);

        Assert::assertEquals($json['meta']['type'], 'user');
        Assert::assertFalse($json['meta']['paginated']);
        Assert::assertEquals('blueMoo',$json['data']['displayName']);
        $this->getContainer()->get('doctrine.orm.entity_manager')->refresh($user);
        Assert::assertEquals('blueMoo', $user->getDisplayName());

    }
}