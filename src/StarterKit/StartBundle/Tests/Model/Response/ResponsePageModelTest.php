<?php

namespace StarterKit\StartBundle\Tests\Model\Response;

use PHPUnit\Framework\Assert;
use StarterKit\StartBundle\Model\Page\PageModel;
use StarterKit\StartBundle\Model\Response\ResponsePageModel;
use StarterKit\StartBundle\Tests\BaseTestCase;

class ResponsePageModelTest extends BaseTestCase
{
    public function testModel()
    {
        $body = [
            'meta' => [
                'type' => 'numbers',
                'paginated' => true,
                'total' => 40,
                'page' => 3,
                'pageSize' => 5,
                'numberOfPages' => 8,
                'lastPage' => false
            ],
            'data' => [1,3,4,5,4]
        ];

        $pageModel = new PageModel([1,3,4,5,4], 3, 40, 5, 'numbers');

        $model = new ResponsePageModel($pageModel);

        Assert::assertEquals($body, $model->getBody());
    }
}