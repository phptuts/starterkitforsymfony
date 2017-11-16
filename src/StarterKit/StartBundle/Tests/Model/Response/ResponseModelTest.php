<?php

namespace StarterKit\StartBundle\Tests\Model\Response;

use PHPUnit\Framework\Assert;
use StarterKit\StartBundle\Model\Response\ResponseModel;
use StarterKit\StartBundle\Tests\BaseTestCase;

class ResponseModelTest extends BaseTestCase
{
    public function testModel()
    {
        $body = [
            'meta' => [
                'type' => 'bluegrass',
                'paginated' => false
            ],
            'data' => [3,3,3]
        ];

        $model = new ResponseModel([3,3,3], 'bluegrass');

        Assert::assertEquals($body, $model->getBody());
    }
}