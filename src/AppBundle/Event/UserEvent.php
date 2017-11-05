<?php

namespace AppBundle\Event;


use AppBundle\Entity\User;
use Symfony\Component\EventDispatcher\Event;

/**
 * Class UserEvent
 * @package AppBundle\Event
 */
class UserEvent extends Event
{
    /**
     * @var User
     */
    private $user;

    /**
     * UserEvent constructor.
     * @param User $user
     */
    public function __construct(User $user)
    {
        $this->user = $user;
    }

    /**
     * @return User
     */
    public function getUser()
    {
        return $this->user;
    }

}