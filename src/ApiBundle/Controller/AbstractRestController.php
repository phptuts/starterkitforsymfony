<?php

namespace ApiBundle\Controller;

use CoreBundle\Model\Response\ResponseModel;
use CoreBundle\Model\Response\ResponseTypeInterface;
use FOS\RestBundle\Controller\FOSRestController;

class AbstractRestController extends FOSRestController
{

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
        return $this->get('startsymfony.core.response_serializer')
                ->serializeResponse(new ResponseModel($data), $groups, $statusCode);
    }
}