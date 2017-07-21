<?php

namespace AppBundle\Service;

use AppBundle\Model\Response\ResponseModelInterface;
use JMS\Serializer\SerializationContext;
use JMS\Serializer\Serializer;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class ResponseSerializer
 * @package AppBundle\Service
 */
class ResponseSerializerService
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
     * @param integer $statusCode
     * @return JsonResponse
     */
    public function serializeResponse(ResponseModelInterface $responseModel, $serializationGroups = [],  $statusCode = Response::HTTP_OK)
    {
        $json = $this->serializer->serialize($responseModel->getBody(), 'json', $this->createSerializationContext($serializationGroups));

        return new JsonResponse($json, $statusCode, [], true);
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