<?php

namespace StarterKit\StartBundle\Tests\Form;

use StarterKit\StartBundle\Form\ChangePasswordType;
use StarterKit\StartBundle\Model\User\ChangePasswordModel;
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

    /**
     * This allows for us mock the AuthorizationCheckerInterface in the form
     * @return array
     */
    protected function getExtensions()
    {
        $form = new ChangePasswordType($this->authorizationChecker);

        return [
            new PreloadedExtension([$form],[]),
        ];
    }

    /**
     * Testing that admin form for changing password don't have current_password
     * This allows admin's to change other user's password
     */
    public function testAdminChangePassword()
    {
        $this->authorizationChecker->shouldReceive('isGranted')->withAnyArgs()->andReturn(true);

        $form = $this->factory->create(ChangePasswordType::class);

        $form->submit(['newPassword' => 'moomoo']);

        Assert::assertTrue($form->isSynchronized());

        $changePasswordModel = new ChangePasswordModel();
        $changePasswordModel->setNewPassword('moomoo');
        Assert::assertEquals($changePasswordModel, $form->getData());

        $view = $form->createView();

        Assert::assertArrayHasKey('newPassword',$view->children);

        Assert::assertArrayNotHasKey('current_password',$view->children);
    }

    /**
     * Tests that regular user have currentPassword field in their
     */
    public function testRegularUserChangePassword()
    {
        $this->authorizationChecker->shouldReceive('isGranted')->withAnyArgs()->andReturn(false);

        $form = $this->factory->create(ChangePasswordType::class);

        $form->submit(['newPassword' => 'moomoo', 'currentPassword' => 'blue']);

        Assert::assertTrue($form->isSynchronized());

        $changePasswordModel = new ChangePasswordModel();
        $changePasswordModel->setNewPassword('moomoo');
        $changePasswordModel->setCurrentPassword('blue');
        Assert::assertEquals($changePasswordModel, $form->getData());

        $view = $form->createView();

        Assert::assertArrayHasKey('newPassword',$view->children);

        Assert::assertArrayHasKey('currentPassword',$view->children);
    }
}