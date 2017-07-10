<?php

namespace Tests\CoreBundle\Service\Credential;

use CoreBundle\Entity\User;
use CoreBundle\Exception\ProgrammerException;
use CoreBundle\Model\Security\AuthTokenModel;
use CoreBundle\Service\Credential\JWSService;
use PHPUnit\Framework\Assert;
use Tests\BaseTestCase;

class JWSServiceTest extends BaseTestCase
{
    /**
     * @var JWSService
     */
    protected $JWSService;

    protected function setUp()
    {
        parent::setUp();
        $this->JWSService = $this->getContainer()->get('startsymfony.core.jws_service');
    }

    public function testServiceName()
    {
        Assert::assertInstanceOf(JWSService::class, $this->getContainer()->get('startsymfony.core.jws_service'));
    }

    public function testAuthModelCreate()
    {
        $user = new User();
        $this->setObjectId($user, 15);

        $model = $this->JWSService->createAuthTokenModel($user);

        $ttl = $this->getContainer()->getParameter('jws_ttl');

        $lessThanExpirationTimeStamp = (new \DateTime())->modify('+' . $ttl - 500 .  ' seconds')->getTimestamp();
        $greaterThanExpirationTimeStamp = (new \DateTime())->modify('+' . $ttl + 500 .  ' seconds')->getTimestamp();

        Assert::assertTrue($lessThanExpirationTimeStamp < $model->getExpirationTimeStamp());
        Assert::assertTrue($greaterThanExpirationTimeStamp > $model->getExpirationTimeStamp());
        Assert::assertTrue($this->JWSService->isValid($model->getToken()));
        $payload = $this->JWSService->getPayLoad($model->getToken());
        Assert::assertEquals(15, $payload['user_id']);
        Assert::assertEquals($model->getExpirationTimeStamp(), $payload['exp']);
        Assert::assertArrayHasKey('iat', $payload);
    }

    public function testInvalidTokenThrowsProgrammerException()
    {
        $this->expectException(ProgrammerException::class);
        $this->expectExceptionCode(ProgrammerException::JWS_INVALID_TOKEN_FORMAT);
        $this->expectExceptionMessage('Unable to read jws token.');
        $this->JWSService->getPayLoad('token');
    }

    /**
     * @dataProvider badTokens
     * @param $token
     */
    public function testInvalidToken($token)
    {
        Assert::assertFalse($this->JWSService->isValid($token));
    }

    public function badTokens()
    {
        return [
            ['token'],
            ['eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJzdWIiOiIxMjM0NTY3ODkwIiwibmFtZSI6IkpvaG4gRG9lIiwiYWRtaW4iOnRydWV9.TJVA95OrM7E2cBab30RMHrHDcEfxjoYZgeFONFh7HgQ']
        ];
    }
}