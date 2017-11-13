<?php


namespace StarterKit\StartBundle\Factory;

/**
 * Interface FactoryInterface
 * @package StarterKit\StartBundle\Factory
 */
interface FactoryInterface
{
    /**
     * Returns the created object
     *
     * @return mixed
     */
    public function getClient();
}