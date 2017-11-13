<?php

namespace StarterKit\StartBundle\Service;

use Symfony\Component\Form\Form;

interface FormSerializerInterface
{
    /**
     * Recursively loops through the form object and returns an array of errors
     *
     * @param Form $data
     * @return array
     */
    public function createFormErrorArray(Form $data);

}