<?php

namespace CoreBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Nelmio\Alice\Fixtures;

/**
 * Class LoadData
 * @package CoreBundle\DataFixtures\ORM
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
               __DIR__ . '/refresh_token.yml'
            ], $manager
        );
    }
}