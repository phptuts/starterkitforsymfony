<?php

namespace Tests\CoreBundle\Model\User;

use CoreBundle\Model\User\ChangePasswordModel;
use PHPUnit\Framework\Assert;
use Tests\BaseTestCase;

class ChangePasswordModelTest extends BaseTestCase
{
    public function testModel()
    {
        $changePasswordModel = new ChangePasswordModel();
        $changePasswordModel->setCurrentPassword('blue');
        $changePasswordModel->setNewPassword('moo');
        Assert::assertEquals('blue',$changePasswordModel->getCurrentPassword());
        Assert::assertEquals('moo',$changePasswordModel->getNewPassword());
    }
}