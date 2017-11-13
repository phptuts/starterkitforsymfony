<?php

namespace StarterKit\StartBundle\Factory;


use Symfony\Component\Intl\Exception\NotImplementedException;
use Symfony\Component\Security\Core\User\UserProviderInterface;

interface UserProviderFactoryInterface
{
    /**
     * Returns the UserProvider based on the type provided
     *
     * @param string $type The
     * @return UserProviderInterface
     * @throws NotImplementedException
     */
    public function getClient($type);
}