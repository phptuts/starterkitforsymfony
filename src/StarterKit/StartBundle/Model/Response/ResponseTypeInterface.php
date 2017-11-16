<?php

namespace StarterKit\StartBundle\Model\Response;

/**
 * Interface ResponseTypeInterface
 * @package StarterKit\StartBundle\Model\Response
 */
interface ResponseTypeInterface
{
    /**
     * Returns the type of response being serialized
     *
     * @return string
     */
    public function getResponseType();
}