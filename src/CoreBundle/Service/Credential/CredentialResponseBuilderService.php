<?php

namespace CoreBundle\Service\Credential;

use CoreBundle\Entity\User;
use CoreBundle\Model\Response\ResponseModel;
use CoreBundle\Service\ResponseSerializerService;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;

class CredentialResponseBuilderService
{
    /**
     * @var CredentialModelBuilderService
     */
    private $credentialModelBuilderService;

    /**
     * @var ResponseSerializerService
     */
    private $responseSerializerService;

    public function __construct(
        CredentialModelBuilderService $credentialModelBuilderService,
        ResponseSerializerService $responseSerializerService
    )
    {
        $this->credentialModelBuilderService = $credentialModelBuilderService;
        $this->responseSerializerService = $responseSerializerService;
    }

    /**
     * Creates a json response that will contain new credentials for the user.
     *
     * @param User $user
     * @return JsonResponse
     */
    public function createCredentialResponse(User $user)
    {
        $credentialModel = $this->credentialModelBuilderService->createCredentialModel($user);

        $jwsResponse = new ResponseModel($credentialModel, ResponseModel::CREDENTIAL_RESPONSE);

        return $this->responseSerializerService
                ->serializeResponse($jwsResponse, [User::USER_PERSONAL_SERIALIZATION_GROUP], Response::HTTP_CREATED);

    }
}