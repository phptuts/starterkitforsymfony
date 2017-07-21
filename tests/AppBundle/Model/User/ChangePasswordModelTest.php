<?php

namespace Tests\AppBundle\Model\User;

use AppBundle\Model\User\ChangePasswordModel;
use PHPUnit\Framework\Assert;
use Tests\BaseTestCase;

class ChangePasswordModelTest extends BaseTestCase
{
    /**
     * Basic password model test
     */
    public function testModel()
    {
        $changePasswordModel = new ChangePasswordModel();
        $changePasswordModel->setCurrentPassword('blue');
        $changePasswordModel->setNewPassword('moo');
        Assert::assertEquals('blue',$changePasswordModel->getCurrentPassword());
        Assert::assertEquals('moo',$changePasswordModel->getNewPassword());
    }
}