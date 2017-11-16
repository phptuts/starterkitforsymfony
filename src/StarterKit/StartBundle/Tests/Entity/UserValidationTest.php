<?php

namespace StarterKit\StartBundle\Test\Entity;

use PHPUnit\Framework\Assert;
use StarterKit\StartBundle\Tests\Entity\User;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use StarterKit\StartBundle\Tests\BaseTestCase;

class UserValidationTest extends BaseTestCase
{
    /**
     * @var ValidatorInterface
     */
    private $validator;

    public function setUp()
    {
        parent::setUp();
        $this->validator = $this->getContainer()->get('validator');
    }

    /**
     * Testing email validation against the User::VALIDATION_GROUP_DEFAULT
     */
    public function testEmailConstraints()
    {

        $user = new User();
        $constraintViolationList = $this->validator->validate($user, null, [User::VALIDATION_GROUP_DEFAULT]);
        Assert::assertEquals('email', $constraintViolationList->get(0)->getPropertyPath());

        $user->setEmail('invalid_email');
        $constraintViolationList = $this->validator->validate($user, null, [User::VALIDATION_GROUP_DEFAULT]);
        Assert::assertEquals('email', $constraintViolationList->get(0)->getPropertyPath());

        $user->setEmail('forget_password_2@gmail.com');
        $constraintViolationList = $this->validator->validate($user, null, [User::VALIDATION_GROUP_DEFAULT]);
        Assert::assertEquals('email', $constraintViolationList->get(0)->getPropertyPath());
    }

    /**
     * Testing plainPassword validation against the User::VALIDATION_GROUP_PLAIN_PASSWORD
     */
    public function testPlainPassword()
    {
        $user = new User();
        $constraintViolationList = $this->validator->validate($user, null, [User::VALIDATION_GROUP_PLAIN_PASSWORD]);
        Assert::assertEquals('plainPassword', $constraintViolationList->get(0)->getPropertyPath());

        $user->setPlainPassword('dd');
        $constraintViolationList = $this->validator->validate($user, null, [User::VALIDATION_GROUP_PLAIN_PASSWORD]);
        Assert::assertEquals('plainPassword', $constraintViolationList->get(0)->getPropertyPath());


        $user->setPlainPassword($this->createString(128));
        $constraintViolationList = $this->validator->validate($user, null, [User::VALIDATION_GROUP_PLAIN_PASSWORD]);
        Assert::assertEquals('plainPassword', $constraintViolationList->get(0)->getPropertyPath());

    }

    /**
     * Testing displayName validation against the User::VALIDATION_GROUP_DEFAULT
     */
    public function testDisplayName()
    {
        $user = new User();
        $user->setEmail('blue@gmail.com')
            ->setDisplayName('bll');

        $constraintViolationList = $this->validator->validate($user, null, [User::VALIDATION_GROUP_DEFAULT]);
        Assert::assertEquals('displayName', $constraintViolationList->get(0)->getPropertyPath());


        $constraintViolationList = $this->validator->validate($user, null, [User::VALIDATION_GROUP_DEFAULT]);
        $user->setDisplayName($this->createString(102));
        Assert::assertEquals('displayName', $constraintViolationList->get(0)->getPropertyPath());

        $constraintViolationList = $this->validator->validate($user, null, [User::VALIDATION_GROUP_DEFAULT]);
        $user->setDisplayName('forgotten');
        Assert::assertEquals('displayName', $constraintViolationList->get(0)->getPropertyPath());

    }

    /**
     * Testing bio validation against the User::VALIDATION_GROUP_DEFAULT
     */
    public function testBio()
    {
        $user = new User();
        $user->setEmail('blue@gmail.com')
            ->setBio($this->createString(3002));

        $constraintViolationList = $this->validator->validate($user, null, [User::VALIDATION_GROUP_DEFAULT]);
        Assert::assertEquals('bio', $constraintViolationList->get(0)->getPropertyPath());
    }

    /**
     * Testing bio validation against the User::VALIDATION_GROUP_DEFAULT & VALIDATION_IMAGE_REQUIRED
     */
    public function testImage()
    {
        $uploadedFile = new UploadedFile(__DIR__ . '/../Mock/image_10Mb.jpg', 'image.jpg');
        $user = new User();
        $user->setEmail('blue@gmail.com')
            ->setImage($uploadedFile);
        $constraintViolationList = $this->validator->validate($user, null, [User::VALIDATION_GROUP_DEFAULT]);
        Assert::assertEquals('image', $constraintViolationList->get(0)->getPropertyPath());

        $user->setImage(null);
        $constraintViolationList = $this->validator->validate($user, null, [User::VALIDATION_IMAGE_REQUIRED]);
        Assert::assertEquals('image', $constraintViolationList->get(0)->getPropertyPath());
    }

    /**
     * Used for creating a really long strings for testing
     * @param $length
     * @return string
     */
    private function createString($length)
    {
        $password = '';
        for ($i = 0; $i <= $length; $i += 1) {
            $password .= $i;
        }
        return $password;

    }


}