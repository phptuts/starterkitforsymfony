<?php

namespace Tests\AppBundle\Service\Credential;

use AppBundle\Entity\User;
use AppBundle\Exception\ProgrammerException;
use Namshi\JOSE\SimpleJWS;
use AppBundle\Service\Credential\JWSService;
use PHPUnit\Framework\Assert;
use Tests\BaseTestCase;

class JWSServiceTest extends BaseTestCase
{
    /**
     * @var JWSService
     */
    protected $JWSService;

    public static $passphrase;

    protected function setUp()
    {
        self::$passphrase = $this->getContainer()->getParameter('app.jws_ttl');
        parent::setUp();
        $this->JWSService =
            new JWSService($this->getContainer()->getParameter('app.jws_pass_phrase'), self::$passphrase);
    }



    /**
     * Tests that the jws service can create an auth model
     */
    public function testAuthModelCreate()
    {
        $user = new User();
        $this->setObjectId($user, 15);

        $model = $this->JWSService->createAuthTokenModel($user);

        $ttl = $this->getContainer()->getParameter('app.jws_ttl');

        $lessThanExpirationTimeStamp = (new \DateTime())->modify('+' . $ttl - 500 .  ' seconds')->getTimestamp();
        $greaterThanExpirationTimeStamp = (new \DateTime())->modify('+' . $ttl + 500 .  ' seconds')->getTimestamp();

        Assert::assertTrue($lessThanExpirationTimeStamp < $model->getExpirationTimeStamp());
        Assert::assertTrue($greaterThanExpirationTimeStamp > $model->getExpirationTimeStamp());
        Assert::assertTrue($this->JWSService->isValid($model->getToken()));
        $payload = $this->JWSService->getPayload($model->getToken());
        Assert::assertEquals(15, $payload['user_id']);
        Assert::assertEquals($model->getExpirationTimeStamp(), $payload['exp']);
        Assert::assertArrayHasKey('iat', $payload);
    }

    /**
     * Tests that a bad token when reading the payload will throw an exception.
     */
    public function testInvalidTokenThrowsProgrammerException()
    {
        $this->expectException(ProgrammerException::class);
        $this->expectExceptionCode(ProgrammerException::JWS_INVALID_TOKEN_FORMAT);
        $this->expectExceptionMessage('Unable to read jws token.');
        $this->JWSService->getPayload('token');
    }

    /**
     * Asserts that bad tokens are not valid
     * @dataProvider badTokens
     * @param $token
     */
    public function testInvalidToken($token)
    {
        Assert::assertFalse($this->JWSService->isValid($token));
    }

    /**
     * A data provider for bad tokens
     * @return array
     */
    public function badTokens()
    {
        return [
            ['token'],
            ['eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJzdWIiOiIxMjM0NTY3ODkwIiwibmFtZSI6IkpvaG4gRG9lIiwiYWRtaW4iOnRydWV9.TJVA95OrM7E2cBab30RMHrHDcEfxjoYZgeFONFh7HgQ']
        ];
    }

    /**
     * Tests that expired tokens are not valid
     */
    public function testExpiredToken()
    {
        $user = new User();
        $this->setObjectId($user, 15);
        $expiredToken = self::createExpiredToken($user);

        Assert::assertFalse($this->JWSService->isValid($expiredToken));
    }

    /**
     * @param User $user
     * @return string
     */
    public static function createExpiredToken(User $user)
    {
        $privateKey = openssl_pkey_get_private(file_get_contents(__DIR__ . '/../../../../var/jwt/private.pem'), self::$passphrase);
        $jws = new SimpleJWS([
            'alg' => 'RS256'
        ]);

        $expirationDate = new \DateTime();
        $expirationDate->modify('-10 seconds');
        $expirationTimestamp = $expirationDate->getTimestamp();

        $jws->setPayload([
            'user_id' => $user->getId(),
            'exp' => $expirationTimestamp,
            'iat' => (new \DateTime())->getTimestamp()
        ]);

        $jws->sign($privateKey);

        return $jws->getTokenString();
    }

}