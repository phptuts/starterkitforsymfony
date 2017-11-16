<?php


namespace StarterKit\StartBundle\Tests\Model\Response;


use PHPUnit\Framework\Assert;
use StarterKit\StartBundle\Model\Response\ResponseFormErrorModel;
use StarterKit\StartBundle\Tests\BaseTestCase;

class ResponseFormErrorModelTest extends BaseTestCase
{
    public function testModel()
    {
        $errors = [
            'meta' => [
                'type' => 'formErrors',
                'paginated' => false,
            ],
            'data' => 'errors'
        ];

        $model = new ResponseFormErrorModel('errors');

        Assert::assertEquals($errors, $model->getBody());
    }
}