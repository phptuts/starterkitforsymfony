<?php

namespace StarterKit\StartBundle\Event;


use StarterKit\StartBundle\Entity\BaseUser;
use Symfony\Component\EventDispatcher\Event;

/**
 * Class UserEvent
 * @package StarterKit\StartBundle\Event
 */
class UserEvent extends Event
{
    /**
     * @var BaseUser
     */
    private $user;

    /**
     * UserEvent constructor.
     * @param BaseUser $user
     */
    public function __construct(BaseUser $user)
    {
        $this->user = $user;
    }

    /**
     * @return BaseUser
     */
    public function getUser()
    {
        return $this->user;
    }

}