<?php


namespace StarterKit\StartBundle\Service;


use StarterKit\StartBundle\Entity\BaseUser;
use Symfony\Component\HttpFoundation\JsonResponse;

interface AuthResponseServiceInterface
{
    /**
     * Creates a json response that will contain new credentials for the user.
     *
     * @param BaseUser $user
     * @return JsonResponse
     */
    public function createAuthResponse(BaseUser $user);
}