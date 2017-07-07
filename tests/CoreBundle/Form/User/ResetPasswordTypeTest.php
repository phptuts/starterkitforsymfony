<?php

namespace Tests\CoreBundle\Form\User;

use CoreBundle\Entity\User;
use CoreBundle\Form\User\ResetPasswordType;
use PHPUnit\Framework\Assert;
use Symfony\Component\Form\Test\TypeTestCase;


class ResetPasswordTypeTest extends TypeTestCase
{
    public function testFormCompiles()
    {
        $form = $this->factory->create(ResetPasswordType::class);
        $form->submit(['plain_password' => 'word']);

        Assert::assertTrue($form->isSynchronized());

        $user = new User();
        $user->setPlainPassword('word');


        Assert::assertEquals($user, $form->getData());

        Assert::assertArrayHasKey('plain_password', $form->createView()->children);
    }
}