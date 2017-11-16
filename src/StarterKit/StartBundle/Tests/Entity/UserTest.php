<?php

namespace StarterKit\StartBundle\Test\Entity;

use PHPUnit\Framework\Assert;
use StarterKit\StartBundle\Model\Auth\AuthTokenModel;
use StarterKit\StartBundle\Tests\Entity\User;
use StarterKit\StartBundle\Tests\BaseTestCase;

/**
 * Class UserTest
 * @package Test\AppBundle\Entity
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

   public function testSmallStuff()
   {
       $user = new User();
       $user->setImageUrl('url');

       Assert::assertEquals('url', $user->getImageUrl());

       $user->setSource('website');

       Assert::assertEquals('website', $user->getSource());
       $user->setCreatedAt(new \DateTime());
       $user->setUpdatedAt(new \DateTime());

       Assert::assertInstanceOf(\DateTime::class, $user->getCreatedAt());
       Assert::assertInstanceOf(\DateTime::class, $user->getUpdatedAt());

   }

   public function testSingleView()
   {
       $user = new User();
       $this->setObjectId($user, 33);
       $user->setDisplayName('blue')
           ->setEmail('email@gmail.com')
           ->setDisplayName('moo')
           ->setBio('bio')
           ->setRoles(['ROLE_USER'])
           ->setImageUrl('url');

       $expected = [
           'id' => 33,
           'displayName' => 'moo',
           'roles' => ['ROLE_USER'],
           'imageUrl' => 'url',
           'email' => 'email@gmail.com',
           'bio' => 'bio'
       ];

       Assert::assertEquals($expected, $user->singleView());
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
       // The AdvancedUserInterface we don't use
       Assert::assertTrue($user->isAccountNonExpired());
       Assert::assertTrue($user->isAccountNonLocked());
       Assert::assertTrue($user->isCredentialsNonExpired());
   }

    /**
     * Tests that prePersists update the createdAt and updatedAt fields
     */
   public function testPrePersist()
   {
       $user = new User();
       $user->prePersist();

       Assert::assertNotEmpty($user->getCreatedAt());
       Assert::assertNotEmpty($user->getUpdatedAt());
   }

    /**
     * Tests that preupdate updates the updatedAt field
     */
   public function testPreUpdate()
   {
       $user = new User();
       $user->preUpdate();

       Assert::assertNotEmpty($user->getUpdatedAt());
   }

   public function testRefreshTokenValidity()
   {
       $user = new User();
       Assert::assertFalse($user->isRefreshTokenValid());

       $expires = (new \DateTime())->modify('-10 seconds');
       $user->setRefreshToken('refresh_token')->setRefreshTokenExpire($expires);
       Assert::assertFalse($user->isRefreshTokenValid());

       $expires = (new \DateTime())->modify('+10 seconds');
       $user->setRefreshTokenExpire($expires);
       Assert::assertTrue($user->isRefreshTokenValid());
   }

   public function testRefreshTokenModel()
   {
       $user = new User();
       $expires = (new \DateTime())->modify('+10 seconds');
       $user->setRefreshTokenExpire($expires)->setRefreshToken('refresh_token');
       Assert::assertTrue($user->isRefreshTokenValid());
       $model = $user->getAuthRefreshModel();
       Assert::assertInstanceOf(AuthTokenModel::class, $model);

       Assert::assertEquals($expires->getTimestamp(), $model->getExpirationTimeStamp());
       Assert::assertEquals('refresh_token', $model->getToken());
   }

}