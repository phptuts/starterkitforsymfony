<?php

namespace Tests\CoreBundle\Form\User;

use CoreBundle\Form\User\ChangePasswordType;
use CoreBundle\Model\User\ChangePasswordModel;
use Mockery\Mock;
use PHPUnit\Framework\Assert;
use Symfony\Component\Form\PreloadedExtension;
use Symfony\Component\Form\Test\TypeTestCase;
use Symfony\Component\Security\Core\Authorization\AuthorizationChecker;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;

class ChangePasswordTypeTest extends TypeTestCase
{
    /**
     * @var AuthorizationChecker|Mock
     */
    private $authorizationChecker;

    protected function setUp()
    {
        $this->authorizationChecker = \Mockery::mock(AuthorizationCheckerInterface::class);

        parent::setUp();
    }

    protected function getExtensions()
    {
        $changePasswordType = new ChangePasswordType($this->authorizationChecker);

        return [
            new PreloadedExtension([$changePasswordType],[]),
        ];
    }

    public function testAdminChangePassword()
    {
        $this->authorizationChecker->shouldReceive('isGranted')->withAnyArgs()->andReturn(true);

        $form = $this->factory->create(ChangePasswordType::class);

        $form->submit(['new_password' => 'moomoo']);

        Assert::assertTrue($form->isSynchronized());

        $changePasswordModel = new ChangePasswordModel();
        $changePasswordModel->setNewPassword('moomoo');
        Assert::assertEquals($changePasswordModel, $form->getData());

        $view = $form->createView();

        Assert::assertArrayHasKey('new_password',$view->children);

        Assert::assertArrayNotHasKey('current_password',$view->children);
    }

    public function testRegularUserChangePassword()
    {
        $this->authorizationChecker->shouldReceive('isGranted')->withAnyArgs()->andReturn(false);

        $form = $this->factory->create(ChangePasswordType::class);

        $form->submit(['new_password' => 'moomoo', 'current_password' => 'blue']);

        Assert::assertTrue($form->isSynchronized());

        $changePasswordModel = new ChangePasswordModel();
        $changePasswordModel->setNewPassword('moomoo');
        $changePasswordModel->setCurrentPassword('blue');
        Assert::assertEquals($changePasswordModel, $form->getData());

        $view = $form->createView();

        Assert::assertArrayHasKey('new_password',$view->children);

        Assert::assertArrayHasKey('current_password',$view->children);
    }
}