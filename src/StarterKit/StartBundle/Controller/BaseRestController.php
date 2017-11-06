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
    /**
     * @var FormSerializer
     */
    private $formSerializer;

    public function __construct(FormSerializer $formSerializer)
    {
        $this->formSerializer = $formSerializer;
    }

    /**
     * Serializes the json response
     *
     * @param ResponseTypeInterface $data the data that is being serialized
     * @param int $statusCode http response status code
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     */
    public function serializeSingleObject(ResponseTypeInterface $data, $statusCode = Response::HTTP_OK)
    {
        $model = new ResponseModel($data);

        return new JsonResponse($model->getBody(), $statusCode);
    }

    /**
     * Serializes a paged response
     *
     * @param PageModel $pageModel
     * @param string $type represents the type
     * @param int $page the current page number
     * @param int $statusCode http response status code
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     */
    public function serializeList(PageModel $pageModel, $type, $page, $statusCode = Response::HTTP_OK)
    {
        $page = new ResponsePageModel($pageModel, $type, $page);

        return new JsonResponse($page->getBody(), $statusCode);
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