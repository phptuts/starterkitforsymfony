<?php

namespace CoreBundle\Service\Credential;

use CoreBundle\Entity\User;
use CoreBundle\Model\Response\ResponseModel;
use CoreBundle\Service\ResponseSerializerService;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;

/**
 * Class CredentialResponseBuilderService
 * @package CoreBundle\Service\Credential
 */
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

        return $this->responseSerializerService
                ->serializeResponse(new ResponseModel($credentialModel), [User::USER_PERSONAL_SERIALIZATION_GROUP], Response::HTTP_CREATED);

    }
}