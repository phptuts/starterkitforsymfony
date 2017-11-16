<?php

namespace StarterKit\StartBundle\Entity;

interface ViewInterface
{

    /**
     * Returns an array of hte view for displaying in a list
     *
     * @return array
     */
    public function listView();

    /**
     * Return an array of the view for displaying as a single item
     *
     * @return array
     */
    public function singleView();
}