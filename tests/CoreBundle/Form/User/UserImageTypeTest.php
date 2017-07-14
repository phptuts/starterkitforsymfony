<?php


namespace Tests\CoreBundle\Form\User;


use CoreBundle\Entity\User;
use CoreBundle\Form\User\UserImageType;
use GuzzleHttp\Psr7\UploadedFile;
use PHPUnit\Framework\Assert;
use Symfony\Component\Form\Test\TypeTestCase;

class UserImageTypeTest extends TypeTestCase
{

    public function testFormCompiles()
    {
        $form = $this->factory->create(UserImageType::class);
        $image = \Mockery::mock(UploadedFile::class);
        $form->submit([ 'image' => $image]);

        Assert::assertTrue($form->isSynchronized());

        $user = new User();
        $user->setImage($image);


        Assert::assertEquals($user, $form->getData());

        Assert::assertArrayHasKey('image', $form->createView()->children);
    }
}