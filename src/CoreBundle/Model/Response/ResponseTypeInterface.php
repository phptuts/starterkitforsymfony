<?php

namespace CoreBundle\Model\Response;

/**
 * Interface ResponseTypeInterface
 * @package CoreBundle\Model\Response
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