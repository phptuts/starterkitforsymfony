<?php


namespace AppBundle\Handler;

use JMS\Serializer\JsonSerializationVisitor;
use Symfony\Component\Form\Form;
use JMS\Serializer\Context;

use FOS\RestBundle\Serializer\Normalizer\FormErrorHandler as FOSRestFormErrorHandler;

/**
 * This is really hard to test so we do it end to end.
 * Class FormErrorHandler
 * @package AppBundle\Handler
 */
class FormErrorHandler extends FOSRestFormErrorHandler
{

    /**
     * This what serializes the form.  It's really complex and uses fos and jms serializer.
     *
     *
     * @param JsonSerializationVisitor $visitor
     * @param Form $form
     * @param array $type
     * @param Context|null $context
     * @return array
     */
    public function serializeFormToJson(JsonSerializationVisitor $visitor, Form $form, array $type, Context $context = null)
    {
        $result = $this->adaptFormArray(parent::serializeFormToJson($visitor, $form, $type));

        $visitor->setRoot($result);

        return $result;
    }

    /**
     * This is what controls the response wrapper.
     *
     * @param \ArrayObject $serializedForm
     * @return array
     */
    protected function adaptFormArray(\ArrayObject $serializedForm)
    {
        return [
            'meta' => [
                'type' => 'formErrors',
                'paginated' => false,
            ],
            'data' => $serializedForm,
        ];
    }

}



