<?php


namespace Tests\CoreBundle\Form\User;


use CoreBundle\Entity\User;
use CoreBundle\Form\User\UpdateUserType;
use PHPUnit\Framework\Assert;
use Symfony\Component\Form\Test\TypeTestCase;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class UpdateUserTypeTest extends TypeTestCase
{
    public function testFormCompiles()
    {
        $form = $this->factory->create(UpdateUserType::class);
        $image = \Mockery::mock(UploadedFile::class);
        $form->submit(['email' => 'moo@gmaol.com', 'display_name' => 'madx', 'image' => $image, 'bio' => 'About me']);

        Assert::assertTrue($form->isSynchronized());

        $user = new User();
        $user->setDisplayName('madx')
            ->setEmail('moo@gmaol.com')
            ->setImage($image)
            ->setBio('About me');


        Assert::assertEquals($user, $form->getData());

        Assert::assertArrayHasKey('email', $form->createView()->children);
        Assert::assertArrayHasKey('display_name', $form->createView()->children);
        Assert::assertArrayHasKey('image', $form->createView()->children);
        Assert::assertArrayHasKey('bio', $form->createView()->children);
    }
}