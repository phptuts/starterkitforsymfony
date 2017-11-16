<?php

namespace StarterKit\StartBundle\Tests\Model\Auth;


use PHPUnit\Framework\Assert;
use StarterKit\StartBundle\Model\Auth\AuthTokenModel;
use StarterKit\StartBundle\Tests\BaseTestCase;

class AuthTokenModelTest extends BaseTestCase
{

    public function testModel()
    {
        $model = new AuthTokenModel('token', 33333);
        Assert::assertEquals($model->getExpirationTimeStamp(), 33333);
        Assert::assertEquals($model->getToken(), 'token');

        $body = [
            'token' => 'token',
            'expirationTimeStamp' => 33333
        ];

        Assert::assertEquals($body, $model->getBody());
    }
}
