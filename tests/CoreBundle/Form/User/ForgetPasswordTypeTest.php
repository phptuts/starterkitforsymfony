<?php

namespace Tests\CoreBundle\Form\User;

use CoreBundle\Entity\User;
use CoreBundle\Form\DataTransformer\UserEmailTransformer;
use CoreBundle\Form\User\ForgetPasswordType;
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

    protected function getExtensions()
    {
        $forgetPasswordType = new ForgetPasswordType($this->transformer);

        return [
            new PreloadedExtension([$forgetPasswordType],[]),
        ];
    }

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