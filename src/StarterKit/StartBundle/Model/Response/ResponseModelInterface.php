<?php

namespace StarterKit\StartBundle\Model\Response;

/**
 * Interface ResponseModelInterface
 * @package StarterKit\StartBundle\Model\Response
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