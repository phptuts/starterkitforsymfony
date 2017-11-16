<?php

namespace StarterKit\StartBundle\Tests\Form;

use StarterKit\StartBundle\Tests\Entity\User;
use StarterKit\StartBundle\Form\DataTransformer\UserEmailTransformer;
use StarterKit\StartBundle\Form\ForgetPasswordType;
use Mockery\Mock;
use PHPUnit\Framework\Assert;
use Symfony\Component\Form\PreloadedExtension;
use Symfony\Component\Form\Test\TypeTestCase;

class ForgetPasswordTypeTest extends TypeTestCase
{
    /**
     * @var UserEmailTransformer|Mock
     */
    private $transformer;

    protected function setUp()
    {
        $this->transformer = \Mockery::mock(UserEmailTransformer::class);

        parent::setUp();
    }

    /**
     * Allows us to mock the transformer
     * @return array
     */
    protected function getExtensions()
    {
        $form = new ForgetPasswordType($this->transformer, User::class);

        return [
            new PreloadedExtension([$form],[]),
        ];
    }

    /**
     * Testing that the form compiles with the right field
     */
    public function testFormCompilesWithTransformer()
    {
        $user = new User();

        $this->transformer->shouldReceive('transform')->with(null)->andReturn($user);
        $this->transformer->shouldReceive('reverseTransform')->with(\Mockery::type(User::class))->andReturn($user);

        $form = $this->factory->create(ForgetPasswordType::class);

        $form->submit(['email' => 'blue']);

        Assert::assertTrue($form->isSynchronized());

        Assert::assertEquals($user, $form->getData());

        Assert::assertArrayHasKey('email', $form->createView()->children);
    }
}