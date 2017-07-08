<?php

namespace Test\CoreBundle\Repository;

use CoreBundle\Entity\User;
use CoreBundle\Exception\ProgrammerException;
use PHPUnit\Framework\Assert;
use Tests\BaseTestCase;

class UserRepositoryTest extends BaseTestCase
{
    public function testDuplicateTokenInDatabase()
    {
        $this->expectException(ProgrammerException::class);
        $this->expectExceptionCode(ProgrammerException::FORGET_PASSWORD_TOKEN_DUPLICATE_EXCEPTION_CODE);
        $this->getContainer()->get('startsymfony.core.repository.user_repository')->findUserByForgetPasswordToken('token');
    }

    public function testCanFindValidForgetPasswordToken()
    {
        $user = $this->getContainer()->get('startsymfony.core.repository.user_repository')->findUserByForgetPasswordToken('token_1');

        Assert::assertInstanceOf(User::class, $user);
        Assert::assertEquals('forget_password_3@gmail.com', $user->getEmail());
    }

    public function testTokenNotFoundReturnsNull()
    {
        $user = $this->getContainer()->get('startsymfony.core.repository.user_repository')->findUserByForgetPasswordToken('token_133');

        Assert::assertNull($user);
    }

    public function testFindUserByEmail()
    {
        $user = $this->getContainer()->get('startsymfony.core.repository.user_repository')->findUserByEmail('forget_password_2@gmail.com');

        Assert::assertInstanceOf(User::class, $user);
    }
}