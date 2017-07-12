<?php


namespace CoreBundle\Handler;

use JMS\Serializer\JsonSerializationVisitor;
use Symfony\Component\Form\Form;
use JMS\Serializer\Context;

use FOS\RestBundle\Serializer\Normalizer\FormErrorHandler as FOSRestFormErrorHandler;

class FormErrorHandler extends FOSRestFormErrorHandler
{
    public function serializeFormToJson(JsonSerializationVisitor $visitor, Form $form, array $type, Context $context = null)
    {
        $isRoot = null === $visitor->getRoot();
        $result = $this->adaptFormArray(parent::serializeFormToJson($visitor, $form, $type));

        if ($isRoot) {
            $visitor->setRoot($result);
        }

        return $result;
    }

    protected function adaptFormArray(\ArrayObject $serializedForm)
    {
        return [
            'meta' => [
                'type' => 'form_errors',
                'paginated' => false,
            ],
            'data' => $serializedForm,
        ];
    }

}



