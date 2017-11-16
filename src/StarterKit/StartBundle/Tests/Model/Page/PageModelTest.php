<?php

namespace StarterKit\StartBundle\Tests\Model\Page;


use PHPUnit\Framework\Assert;
use StarterKit\StartBundle\Model\Page\PageModel;
use StarterKit\StartBundle\Tests\BaseTestCase;

class PageModelTest extends BaseTestCase
{
    public function testModel()
    {
        $model = new PageModel([1,3,4,5,4], 3, 40, 5, 'numbers');
        Assert::assertEquals([1,3,4,5,4], $model->getResults());
        Assert::assertEquals('numbers', $model->getType());
        Assert::assertEquals(3, $model->getCurrentPage());
        Assert::assertEquals(40, $model->getTotal());
        Assert::assertEquals(5, $model->getPageSize());
    }
}