<?php

namespace CoreBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Nelmio\Alice\Fixtures;

class LoadData implements FixtureInterface
{
    public function load(ObjectManager $manager)
    {
        Fixtures::load([
                __DIR__ . '/users.yml'
            ], $manager
        );
    }
}