<?php


namespace StarterKit\StartBundle\Controller;


use StarterKit\StartBundle\Model\Page\PageModel;
use StarterKit\StartBundle\Model\Response\ResponseModel;
use StarterKit\StartBundle\Model\Response\ResponsePageModel;
use StarterKit\StartBundle\Model\Response\ResponseTypeInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

trait ControllerTrait
{
    /**
     * Serializes the json response
     *
     * @param array $data
     * @param string $type
     * @param int $statusCode http response status code
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     */
    public function serializeSingleObject(array $data, $type,  $statusCode = Response::HTTP_OK)
    {
        $model = new ResponseModel($data, $type);

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
}