<?php

namespace Tests\CoreBundle\Form\User;

use CoreBundle\Entity\User;
use CoreBundle\Form\User\RegisterType;
use PHPUnit\Framework\Assert;
use Symfony\Component\Form\Test\TypeTestCase;

class RegisterTypeTest extends TypeTestCase
{
    public function testFormCompiles()
    {
        $form = $this->factory->create(RegisterType::class);
        $form->submit(['email' => 'moo@gmaol.com', 'plainPassword' => 'masdfasd']);

        Assert::assertTrue($form->isSynchronized());

        $user = new User();
        $user->setPlainPassword('masdfasd')
            ->setEmail('moo@gmaol.com');

        Assert::assertEquals($user, $form->getData());

        Assert::assertArrayHasKey('email', $form->createView()->children);
        Assert::assertArrayHasKey('plainPassword', $form->createView()->children);
    }
}
