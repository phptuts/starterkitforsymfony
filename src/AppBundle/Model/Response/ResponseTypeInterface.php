<?php

namespace AppBundle\Model\Response;

/**
 * Interface ResponseTypeInterface
 * @package AppBundle\Model\Response
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