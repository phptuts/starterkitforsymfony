<?php

namespace AppBundle\Model\Response;

/**
 * Interface ResponseModelInterface
 * @package AppBundle\Model\Response
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