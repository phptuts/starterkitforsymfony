<?php

namespace StarterKit\StartBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Nelmio\Alice\Fixtures;

/**
 * Class LoadData
 * @package AppBundle\DataFixtures\ORM
 */
class LoadData implements FixtureInterface
{
    /**
     * @param ObjectManager $manager
     */
    public function load(ObjectManager $manager)
    {
        Fixtures::load([
            __DIR__ . '/users.yml',
            ], $manager
        );
    }
}