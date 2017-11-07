<?php

namespace StarterKit\StartBundle\Tests\Model\Credential;

use PHPUnit\Framework\Assert;
use StarterKit\StartBundle\Model\Credential\CredentialInterface;
use StarterKit\StartBundle\Model\Credential\CredentialEmailModel;
use StarterKit\StartBundle\Tests\BaseTestCase;

class CredentialEmailModelTest extends BaseTestCase
{
    public function testModel()
    {
        $model = new CredentialEmailModel('email', 'password');
        Assert::assertEquals('email', $model->getUserIdentifier());
        Assert::assertEquals(CredentialInterface::PROVIDER_TYPE_EMAIL, $model->getProvider());
        Assert::assertEquals('email',$model->getEmail());
        Assert::assertEquals('password', $model->getPassword());
    }

}