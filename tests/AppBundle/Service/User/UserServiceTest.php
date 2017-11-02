<?php

namespace Tests\AppBundle\Service\User;

use AppBundle\Entity\User;
use AppBundle\Exception\ProgrammerException;
use AppBundle\Service\User\UserService;
use Doctrine\ORM\EntityManager;
use Mockery\Mock;
use PHPUnit\Framework\Assert;
use Symfony\Component\Security\Core\Encoder\EncoderFactory;
use Symfony\Component\Security\Core\Encoder\PasswordEncoderInterface;
use Tests\BaseTestCase;

class UserServiceTest extends BaseTestCase
{
    /**
     * @var EntityManager|Mock
     */
    protected $em;

    /**
     * @var EncoderFactory|Mock
     */
    protected $encoderFactory;

    /**
     * @var UserService
     */
    protected $userService;

    public function setUp()
    {
        $this->encoderFactory = \Mockery::mock(EncoderFactory::class);
        $this->em = \Mockery::mock(EntityManager::class);
        $repository = $this->getContainer()->get('doctrine')->getRepository(User::class);
        $this->userService = new UserService($this->em, $this->encoderFactory, $repository, 1000);
    }


    /**
     * Tests that if the plain password is not set that an exception is thrown
     * This method is used to save a user with a new password / never should should save a blank password.
     */
    public function testSavePlainPasswordNotSet()
    {
        $user = new User();
        $user->setPlainPassword(null);
        $this->expectException(ProgrammerException::class);
        $this->expectExceptionCode(ProgrammerException::NO_PLAIN_PASSWORD_ON_USER_ENTITY_EXCEPTION_CODE);

        $this->userService->saveUserWithPlainPassword($user);
    }

    /**
     * Tests that save plain encodes the plain password
     */
    public function testSavePlainPassword()
    {
        $user = new User();
        $user->setPlainPassword('password');

        $encoder = \Mockery::mock(PasswordEncoderInterface::class);
        $encoder->shouldReceive('encodePassword')->with('password', null)->andReturn('adfasdf3dafsa');
        $this->encoderFactory->shouldReceive('getEncoder')->with($user)->andReturn($encoder);

        $this->em->shouldReceive('persist')->once()->with($user);
        $this->em->shouldReceive('flush')->once();

        $this->userService->saveUserWithPlainPassword($user);

        Assert::assertEquals('adfasdf3dafsa', $user->getPassword());
    }

    /**
     * Test that reset password saves the password and null forget password and expiration date
     */
    public function testResetPassword()
    {
        $user = new User();
        $user->setPlainPassword('password')->setForgetPasswordToken('token')->setForgetPasswordExpired(new \DateTime());

        $encoder = \Mockery::mock(PasswordEncoderInterface::class);
        $encoder->shouldReceive('encodePassword')->with('password', null)->andReturn('adfasdf3dafsa');
        $this->encoderFactory->shouldReceive('getEncoder')->with($user)->andReturn($encoder);

        $this->em->shouldReceive('persist')->once()->with($user);
        $this->em->shouldReceive('flush')->once();

        $this->userService->saveUserForResetPassword($user);

        Assert::assertEquals('adfasdf3dafsa', $user->getPassword());
        Assert::assertEmpty($user->getForgetPasswordToken());
        Assert::assertEmpty($user->getForgetPasswordExpired());
    }

    /**
     * Tests that a  special exception is thrown when duplicate forget password token are found in the database
     * This should never happen so we want to know about it
     */
    public function testDuplicateTokenInDatabase()
    {
        $this->expectException(ProgrammerException::class);
        $this->expectExceptionCode(ProgrammerException::FORGET_PASSWORD_TOKEN_DUPLICATE_EXCEPTION_CODE);
        $this->userService->findUserByForgetPasswordToken('token');
    }

    /**
     * This just tests that it can hit the database and find a valid forget password reset token
     */
    public function testCanFindValidForgetPasswordToken()
    {
        $user = $this->userService->findUserByForgetPasswordToken('token_1');

        Assert::assertInstanceOf(User::class, $user);
        Assert::assertEquals('forget_password_3@gmail.com', $user->getEmail());
    }

    /**
     * Tests that if password reset token is not found it return null
     */
    public function testTokenNotFoundReturnsNull()
    {
        $user = $this->userService->findUserByForgetPasswordToken('token_133');

        Assert::assertNull($user);
    }

    /**
     * Tests that we can find a user by email
     */
    public function testFindUserByEmail()
    {
        $user = $this->userService->findUserByEmail('forget_password_2@gmail.com');

        Assert::assertInstanceOf(User::class, $user);
    }

    /**
     * Tests that if Email Does exist this function returns true
     */
    public function testDoesEmailExist()
    {
        Assert::assertTrue( $this->userService->doesEmailExist('forget_password_2@gmail.com'));
        Assert::assertFalse( $this->userService->doesEmailExist('forget_password_2' . time() . '@gmail.com'));
    }

    /**
     * Tests the paginator return for searching for users
     * We do a basic search test
     */
    public function testGetUser()
    {
        $pageUser = $this->userService->searchUser('forget_password');
        Assert::assertEquals(4, $pageUser->count());

        $pageUser = $this->userService->searchUser('forget_password_2');
        Assert::assertEquals(1, $pageUser->count());
    }

    /**
     * Tests that a user can be found by user id
     */
    public function testFindUserById()
    {
        $user = $this->userService->findUserByEmail('forget_password_2@gmail.com');

        $userFoundById = $this->userService->findUserById($user->getId());

        Assert::assertEquals($user->getId(), $userFoundById->getId());
    }

    /**
     * Tests that a user can be found by facebook user id
     */
    public function testFindByFacebookUserId()
    {
        Assert::assertInstanceOf(User::class, $this->userService->findByFacebookUserId('facebook_test_user_id32'));
    }

    /**
     * Tests that a user can be found by google user id
     */
    public function testFindUserByGoogleUserId()
    {
        Assert::assertInstanceOf(User::class, $this->userService->findByGoogleUserId('google_user_id_dsf3'));
    }
}