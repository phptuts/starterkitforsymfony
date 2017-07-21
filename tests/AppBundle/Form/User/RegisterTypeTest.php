<?php

namespace Tests\AppBundle\Form\User;

use AppBundle\Entity\User;
use AppBundle\Form\User\RegisterType;
use PHPUnit\Framework\Assert;
use Symfony\Component\Form\Test\TypeTestCase;

class RegisterTypeTest extends TypeTestCase
{
    /**
     * Testing that the form compiles with the right field
     */
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
