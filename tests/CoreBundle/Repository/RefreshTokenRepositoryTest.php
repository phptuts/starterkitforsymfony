<?php


namespace Test\CoreBundle\Repository;

use CoreBundle\Entity\RefreshToken;
use CoreBundle\Exception\ProgrammerException;
use CoreBundle\Repository\RefreshTokenRepository;
use PHPUnit\Framework\Assert;
use Tests\BaseTestCase;

class RefreshTokenRepositoryTest extends BaseTestCase
{
    /**
     * @var RefreshTokenRepository
     */
    protected $repository;

    public function setUp()
    {
        parent::setUp();
        $this->repository = $this->getContainer()->get('startsymfony.core.repository.refreshtoken_repository');
    }

    public function testDuplicateRefreshToken()
    {
        $this->expectException(ProgrammerException::class);
        $this->expectExceptionCode(ProgrammerException::REFRESH_TOKEN_DUPLICATE);

        $this->repository->getValidRefreshToken('token_dup');
    }

    public function testExpiredToken()
    {
        Assert::assertNull($this->repository->getValidRefreshToken('token_expired'));
    }

    public function testUsedToken()
    {
        Assert::assertNull($this->repository->getValidRefreshToken('used_token'));
    }

    public function testValidToken()
    {
        Assert::assertInstanceOf(RefreshToken::class, $this->repository->getValidRefreshToken('valid_refresh_token'));
    }
}