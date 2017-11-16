<?php

namespace StarterKit\StartBundle\Event;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\EventDispatcher\Event;

/**
 * Class AuthFailedEvent
 * @package StarterKit\StartBundle\Event
 */
class AuthFailedEvent extends Event
{
    /**
     * @var Request
     */
    protected $request;

    /**
     * @var AuthenticationException
     */
    protected $exception;

    /**
     * AuthenticationFailedEvent constructor.
     * @param Request $request
     * @param AuthenticationException $exception
     */
    public function __construct(Request $request, AuthenticationException $exception)
    {
        $this->request = $request;
        $this->exception = $exception;
    }

    /**
     * @return Request
     */
    public function getRequest()
    {
        return $this->request;
    }

    /**
     * @return AuthenticationException
     */
    public function getException()
    {
        return $this->exception;
    }
}