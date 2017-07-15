<?php

namespace Test\CoreBundle\Entity;

use CoreBundle\Entity\User;
use PHPUnit\Framework\Assert;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Tests\BaseTestCase;

/**
 * Class UserTest
 * @package Test\CoreBundle\Entity
 */
class UserTest extends BaseTestCase
{

    /**
     * Tests that a role is always returned
     */
   public function testGetUserRoles()
   {
        $user = new User();
        Assert::assertEquals(["ROLE_USER"], $user->getRoles());
   }

    /**
     * Test that get user name always returns the email
     * This is done for auth purpose we want an email only login
     */
   public function testGetUserName()
   {
        $user = new User();
        $user->setEmail('blue@gmail.com');
        Assert::assertEquals('blue@gmail.com', $user->getUsername());
   }

    /**
     * isEqualTo should say that 2 users that have the same email are equal
     */
   public function testIsEqualTo()
   {
       $user = new User();
       $user->setEmail('blue@gmail.com');

       $user2 = new User();
       $user2->setEmail('blue@gmail.com');

       Assert::assertTrue($user->isEqualTo($user2));
   }

    /**
     * Test that erase creds nulls the plain password field
     * This is done for security
     */
   public function testEraseCreds()
   {
       $user = new User();
       $user->setPlainPassword('blah');
       $user->eraseCredentials();

       Assert::assertNull($user->getPlainPassword());
   }

    /**
     * Basic user test to make sure that all model is valid
     * Because we only use a little of the AdvancedUserInterface we make sure the ones we don't use return true
     */
   public function testBasics()
   {
       $user = new User();
       $this->setObjectId($user, 3);
       $user->setBio('blah');
       $user->setImageUrl('url');
       $user->setDisplayName('name');
       $user->setSource('website');
       $image = \Mockery::mock(UploadedFile::class);
       $user->setImage($image);

       $user2 = new User();
       $user2->unserialize($user->serialize());

       Assert::assertEquals('blah', $user->getBio());
       Assert::assertEquals($image, $user->getImage());
       Assert::assertEquals('url', $user->getImageUrl());
       Assert::assertEquals('name', $user->getDisplayName());
       Assert::assertEquals('website', $user->getSource());
       Assert::assertEquals($user->getEmail(), $user2->getEmail());
       Assert::assertEquals(3,$user->getId());

       // The AdvancedUserInterface we don't use
       Assert::assertTrue($user->isAccountNonExpired());
       Assert::assertTrue($user->isAccountNonLocked());
       Assert::assertTrue($user->isCredentialsNonExpired());


   }
}