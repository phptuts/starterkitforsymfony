<?php

namespace StarterKit\StartBundle\Tests\Form;


use StarterKit\StartBundle\Tests\Entity\User;
use StarterKit\StartBundle\Form\UpdateUserType;
use PHPUnit\Framework\Assert;
use Symfony\Component\Form\PreloadedExtension;
use Symfony\Component\Form\Test\TypeTestCase;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class UpdateUserTypeTest extends TypeTestCase
{
    /**
     * Allows us to mock the transformer
     * @return array
     */
    protected function getExtensions()
    {
        $form = new UpdateUserType(User::class);

        return [
            new PreloadedExtension([$form],[]),
        ];
    }

    /**
     * Testing that the form compiles with the right field
     * If the api option is false we should see the image field
     */
    public function testFormCompiles()
    {
        $form = $this->factory->create(UpdateUserType::class);
        $image = \Mockery::mock(UploadedFile::class);
        $form->submit(['email' => 'moo@gmaol.com', 'displayName' => 'madx', 'image' => $image, 'bio' => 'About me']);

        Assert::assertTrue($form->isSynchronized());

        $user = new User();
        $user->setDisplayName('madx')
            ->setEmail('moo@gmaol.com')
            ->setImage($image)
            ->setBio('About me');


        Assert::assertEquals($user, $form->getData());

        Assert::assertArrayHasKey('email', $form->createView()->children);
        Assert::assertArrayHasKey('displayName', $form->createView()->children);
        Assert::assertArrayHasKey('image', $form->createView()->children);
        Assert::assertArrayHasKey('bio', $form->createView()->children);
    }

    /**
     * Testing that the form compiles with the right field
     * If the api option is true the image field should not be their
     */
    public function testApiRemovesImageFromForm()
    {
        $form = $this->factory->create(UpdateUserType::class, null, ['api' => true]);

        Assert::assertArrayHasKey('email', $form->createView()->children);
        Assert::assertArrayHasKey('displayName', $form->createView()->children);
        Assert::assertArrayNotHasKey('image', $form->createView()->children);
        Assert::assertArrayHasKey('bio', $form->createView()->children);
    }
}