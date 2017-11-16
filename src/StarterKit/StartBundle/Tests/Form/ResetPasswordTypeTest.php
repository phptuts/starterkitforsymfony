<?php

namespace StarterKit\StartBundle\Tests\Form;

use StarterKit\StartBundle\Tests\Entity\User;
use StarterKit\StartBundle\Form\ResetPasswordType;
use PHPUnit\Framework\Assert;
use Symfony\Component\Form\PreloadedExtension;
use Symfony\Component\Form\Test\TypeTestCase;


class ResetPasswordTypeTest extends TypeTestCase
{
    /**
     * Allows us to mock the transformer
     * @return array
     */
    protected function getExtensions()
    {
        $form = new ResetPasswordType(User::class);

        return [
            new PreloadedExtension([$form],[]),
        ];
    }


    /**
     * Testing that the form compiles with the right field
     */
    public function testFormCompiles()
    {
        $form = $this->factory->create(ResetPasswordType::class);
        $form->submit(['plainPassword' => 'word']);

        Assert::assertTrue($form->isSynchronized());

        $user = new User();
        $user->setPlainPassword('word');


        Assert::assertEquals($user, $form->getData());

        Assert::assertArrayHasKey('plainPassword', $form->createView()->children);
    }
}