<?php

namespace AppBundle\Controller\Api;

use AppBundle\Model\Page\PageModel;
use AppBundle\Model\Response\ResponseModel;
use AppBundle\Model\Response\ResponsePageModel;
use AppBundle\Model\Response\ResponseTypeInterface;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

class BaseRestController extends Controller
{


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
}