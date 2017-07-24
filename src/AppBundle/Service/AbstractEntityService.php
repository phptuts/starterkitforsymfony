<?php

namespace AppBundle\Service;

use Doctrine\ORM\EntityManagerInterface;

abstract class AbstractEntityService
{
    /**
     * @var EntityManagerInterface
     */
    protected $em;

    /**
     * AbstractEntityService constructor.
     * @param EntityManagerInterface $em
     */
    public function __construct(EntityManagerInterface $em)
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