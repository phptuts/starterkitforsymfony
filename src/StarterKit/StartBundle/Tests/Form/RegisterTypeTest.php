<?php

namespace StarterKit\StartBundle\Tests\Form;

use StarterKit\StartBundle\Tests\Entity\User;
use StarterKit\StartBundle\Form\RegisterType;
use PHPUnit\Framework\Assert;
use Symfony\Component\Form\PreloadedExtension;
use Symfony\Component\Form\Test\TypeTestCase;

class RegisterTypeTest extends TypeTestCase
{
    /**
     * Allows us to mock the transformer
     * @return array
     */
    protected function getExtensions()
    {
        $form = new RegisterType(User::class);

        return [
            new PreloadedExtension([$form],[]),
        ];
    }

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
