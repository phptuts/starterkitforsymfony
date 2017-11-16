<?php

namespace StarterKit\StartBundle\Tests;

use Liip\FunctionalTestBundle\Test\WebTestCase;

class BaseTestCase extends WebTestCase
{
    /**
     * @link https://github.com/mockery/mockery/issues/376
     */
    protected function tearDown()
    {
        if ($container = \Mockery::getContainer()) {
            $this->addToAssertionCount($container->mockery_getExpectationCount());
            \Mockery::close();
        }
    }

    public function setObjectId(&$object, $id)
    {
        $refObject   = new \ReflectionObject( $object );
        $refProperty = $refObject->getProperty( 'id' );
        $refProperty->setAccessible( true );
        $refProperty->setValue($object, $id);
        $refProperty->setAccessible(false);
    }
}