<?php

namespace StarterKit\StartBundle\Controller;

use StarterKit\StartBundle\Model\Page\PageModel;
use StarterKit\StartBundle\Model\Response\ResponseFormErrorModel;
use StarterKit\StartBundle\Model\Response\ResponseModel;
use StarterKit\StartBundle\Model\Response\ResponsePageModel;
use StarterKit\StartBundle\Model\Response\ResponseTypeInterface;
use StarterKit\StartBundle\Service\FormSerializer;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Form\Form;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

class BaseRestController extends Controller
{
    use ControllerTrait;

    /**
     * @var FormSerializer
     */
    private $formSerializer;

    public function __construct(FormSerializer $formSerializer)
    {
        $this->formSerializer = $formSerializer;
    }

    /**
     * Returns a serialized error response
     *
     * @param Form $form
     * @return JsonResponse
     */
    public function serializeFormError(Form $form)
    {
        $errors = $this->formSerializer->createFormErrorArray($form);

        $responseModel = new ResponseFormErrorModel($errors);

        return new JsonResponse($responseModel->getBody(), Response::HTTP_BAD_REQUEST);
    }
}