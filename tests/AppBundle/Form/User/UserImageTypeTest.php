<?php


namespace Tests\AppBundle\Form\User;


use AppBundle\Entity\User;
use AppBundle\Form\User\UserImageType;
use GuzzleHttp\Psr7\UploadedFile;
use PHPUnit\Framework\Assert;
use Symfony\Component\Form\Test\TypeTestCase;

class UserImageTypeTest extends TypeTestCase
{

    /**
     * Testing that the form compiles with the right field
     */
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