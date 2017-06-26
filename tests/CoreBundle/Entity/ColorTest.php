<?php

namespace Test\Entity;

use CoreBundle\Entity\Color;
use PHPUnit\Framework\Assert;
use Tests\BaseTestCase;

class ColorTest extends BaseTestCase
{
    public function testColorBasics()
    {
        $color = new Color();
        $color->setColor('blue');
        $this->setObjectId($color, 3);
        Assert::assertEquals(3, $color->getId());
        Assert::assertEquals('blue', $color->getColor());
    }
}