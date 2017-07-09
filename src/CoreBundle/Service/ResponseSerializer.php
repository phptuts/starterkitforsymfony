<?php

namespace CoreBundle\Service;

use CoreBundle\Model\Response\ResponseModelInterface;
use JMS\Serializer\SerializationContext;
use JMS\Serializer\Serializer;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class ResponseSerializer
 * @package CoreBundle\Service
 */
class ResponseSerializer
{
    /**
     * @var Serializer
     */
    private $serializer;

    public function __construct(Serializer $serializer)
    {
        $this->serializer = $serializer;
    }

    /**
     * @param ResponseModelInterface $responseModel
     * @param array $serializationGroups
     * @param $statusCode
     * @return JsonResponse
     */
    public function serializeResponse(ResponseModelInterface $responseModel, $serializationGroups = [],  $statusCode = Response::HTTP_OK)
    {
        $json = $this->serializer->serialize($responseModel->toArray(), 'array', $this->createSerializationContext($serializationGroups));

        return new JsonResponse($json, $statusCode);
    }

    /**
     * @param $groups
     * @return SerializationContext
     */
    private function createSerializationContext($groups)
    {
        $groups[] = 'Default';

        $context = new SerializationContext();
        $context->setGroups($groups)
            ->setSerializeNull(true);

        return $context;
    }
}