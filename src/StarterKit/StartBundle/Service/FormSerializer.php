<?php

namespace StarterKit\StartBundle\Service;

use Symfony\Component\Form\Form;
use Symfony\Component\Form\FormError;
use Symfony\Component\Translation\TranslatorInterface;

/**
 * Class FormSerializer
 * @package StarterKit\StartBundle\Service
 */
class FormSerializer
{

    /**
     * @var TranslatorInterface
     */
    private $translator;

    /**
     * FormSerializer constructor.
     * @param TranslatorInterface $translator
     */
    public function __construct(TranslatorInterface $translator)
    {
        $this->translator = $translator;
    }


    /**
     * Recursively loops through the form object and returns an array of errors
     *
     * @param Form $data
     * @return array
     */
    public function createFormErrorArray(Form $data)
    {
        $form = [];
        $errors = [];
        foreach ($data->getErrors() as $error) {
            $errors[] = $this->getErrorMessage($error);
        }

        if ($errors) {
            $form['errors'] = $errors;
        }

        $children = [];
        foreach ($data->all() as $child) {
            if ($child instanceof Form) {
                $children[$child->getName()] = $this->createFormErrorArray($child);
            }
        }

        if ($children) {
            $form['children'] = $children;
        }

        return $form;
    }

    /**
     * Return the error message translated
     *
     * @param FormError $error
     * @return string
     */
    private function getErrorMessage(FormError $error)
    {
        if (null !== $error->getMessagePluralization()) {
            return $this->translator
                ->transChoice(
                    $error->getMessageTemplate(),
                    $error->getMessagePluralization(),
                    $error->getMessageParameters(),
                    'validators'
                );
        }

        return $this->translator->trans($error->getMessageTemplate(), $error->getMessageParameters(), 'validators');
    }
}