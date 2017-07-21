<?php

namespace AppBundle\Service;

use Doctrine\ORM\EntityManager;

abstract class AbstractEntityService
{
    /**
     * @var EntityManager
     */
    protected $em;

    /**
     * AbstractEntityService constructor.
     * @param EntityManager $em
     */
    public function __construct(EntityManager $em)
    {
        $this->em = $em;
    }

    /**
     * Saves an entity
     *
     * @param $entity
     */
    public function save($entity)
    {
        $this->em->persist($entity);
        $this->em->flush();
    }
}