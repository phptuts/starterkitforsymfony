<?php

namespace Test\CoreBundle\Repository;

use CoreBundle\Entity\User;
use CoreBundle\Exception\ProgrammerException;
use CoreBundle\Repository\UserRepository;
use PHPUnit\Framework\Assert;
use Tests\BaseTestCase;

class UserRepositoryTest extends BaseTestCase
{
    /**
     * @var UserRepository
     */
    protected $repository;

    public function setUp()
    {
        parent::setUp();
        $this->repository = $this->getContainer()->get('startsymfony.core.repository.user_repository');
    }


    public function testDuplicateTokenInDatabase()
    {
        $this->expectException(ProgrammerException::class);
        $this->expectExceptionCode(ProgrammerException::FORGET_PASSWORD_TOKEN_DUPLICATE_EXCEPTION_CODE);
        $this->repository = $this->repository->findUserByForgetPasswordToken('token');
    }

    public function testCanFindValidForgetPasswordToken()
    {
        $user = $this->repository->findUserByForgetPasswordToken('token_1');

        Assert::assertInstanceOf(User::class, $user);
        Assert::assertEquals('forget_password_3@gmail.com', $user->getEmail());
    }

    public function testTokenNotFoundReturnsNull()
    {
        $user = $this->repository->findUserByForgetPasswordToken('token_133');

        Assert::assertNull($user);
    }

    public function testFindUserByEmail()
    {
        $user = $this->repository->findUserByEmail('forget_password_2@gmail.com');

        Assert::assertInstanceOf(User::class, $user);
    }
    
    public function testDoesEmailExist()
    {
        Assert::assertTrue( $this->repository->doesEmailExist('forget_password_2@gmail.com'));
        Assert::assertFalse( $this->repository->doesEmailExist('forget_password_2' . time() . '@gmail.com'));
    }

    public function testGetUser()
    {
        $pageUser = $this->repository->getUsers('forget_password');
        Assert::assertEquals(3, $pageUser->count());

        $pageUser = $this->repository->getUsers('forget_password_2');
        Assert::assertEquals(1, $pageUser->count());
    }
}