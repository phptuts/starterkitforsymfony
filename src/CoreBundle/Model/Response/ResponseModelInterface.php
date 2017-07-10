<?php

namespace CoreBundle\Model\Response;

/**
 * Interface ResponseModelInterface
 * @package CoreBundle\Model\Response
 */
interface ResponseModelInterface
{
    /**
     * Returns an array representing the response model
     *
     * @return array
     */
    public function getBody();
}