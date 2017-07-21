<?php


namespace Test\AppBundle\Entity;


use AppBundle\Entity\RefreshToken;
use PHPUnit\Framework\Assert;
use Tests\BaseTestCase;

class RefreshTokenTest extends BaseTestCase
{
    /**
     * Vanity test for code coverage
     */
    public function testGetId()
    {
        $refreshToken = new RefreshToken();
        $this->setObjectId($refreshToken, 13);

        Assert::assertEquals(13, $refreshToken->getId());
    }

}