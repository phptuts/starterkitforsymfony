<?php


namespace StarterKit\StartBundle\Tests;


use Mockery\Mock;
use StarterKit\StartBundle\StarterKitStartBundle;
use Symfony\Component\DependencyInjection\ContainerBuilder;

class StarterKitStartBundleTest extends BaseTestCase
{
    /**
     * @var ContainerBuilder|Mock
     */
    protected $containerBuilder;

    /**
     * @var StarterKitStartBundle
     */
    protected $bundle;

    public function setUp()
    {
        parent::setUp();
        $this->containerBuilder = \Mockery::mock(ContainerBuilder::class);
        $this->bundle = new StarterKitStartBundle();
    }

    public function testThatProductionEnvironmentDoesNotAddCompilerPass()
    {
        $this->containerBuilder->shouldReceive('getParameter')->with('kernel.environment')->andReturn('prod');
        $this->containerBuilder->shouldReceive('addCompilerPass')->withAnyArgs()->never();
        $this->bundle->build($this->containerBuilder);
    }

    public function testThatTestEnvironmentDoesCompilerPass()
    {
        $this->containerBuilder->shouldReceive('getParameter')->with('kernel.environment')->andReturn('test');
        $this->containerBuilder->shouldReceive('addCompilerPass')->withAnyArgs()->once();
        $this->bundle->build($this->containerBuilder);
    }
}