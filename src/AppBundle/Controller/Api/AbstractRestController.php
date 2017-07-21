<?php

namespace AppBundle\Controller\Api;

use AppBundle\Model\Response\ResponseModel;
use AppBundle\Model\Response\ResponsePageModel;
use AppBundle\Model\Response\ResponseTypeInterface;
use AppBundle\Service\ResponseSerializerService;
use Doctrine\ORM\Tools\Pagination\Paginator;
use FOS\RestBundle\Controller\FOSRestController;

class AbstractRestController extends FOSRestController
{

    /**
     * @var ResponseSerializerService
     */
    protected $responseSerializer;

    public function __construct(ResponseSerializerService $responseSerializer)
    {
        $this->responseSerializer = $responseSerializer;
    }

    /**
     * Serializes the json response
     *
     * @param ResponseTypeInterface $data the data that is being serialized
     * @param array $groups jms serialization groups
     * @param int $statusCode http response status code
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     */
    public function serializeSingleObject(ResponseTypeInterface $data, $groups = [], $statusCode)
    {
        return $this->responseSerializer
                ->serializeResponse(new ResponseModel($data), $groups, $statusCode);
    }

    /**
     * Serializes a paged response
     *
     * @param Paginator $paginator doctrine page object
     * @param string $type represents the type
     * @param int $page the current page number
     * @param array $groups jms serialization
     * @param int $statusCode http response status code
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     */
    public function serializeList(Paginator $paginator, $type, $page, $groups = [], $statusCode)
    {
        return $this->responseSerializer->serializeResponse(
            new ResponsePageModel($paginator, $type, $page),
            $groups,
            $statusCode
        );
    }
}