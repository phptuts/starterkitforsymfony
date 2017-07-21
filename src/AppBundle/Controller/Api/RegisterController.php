<?php

namespace AppBundle\Controller\Api;

use AppBundle\Form\User\RegisterType;
use AppBundle\Service\Credential\CredentialResponseBuilderService;
use AppBundle\Service\User\RegisterService;
use FOS\RestBundle\Controller\FOSRestController;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use FOS\RestBundle\Controller\Annotations as REST;

/**
 * Class RegisterController
 * @package ApiBundle\Controller\Api
 * @REST\NamePrefix("api_users_")
 */
class RegisterController extends FOSRestController
{
    /**
     * @var RegisterService
     */
    private $registerService;

    /**
     * @var CredentialResponseBuilderService
     */
    private $credentialResponseBuilderService;

    /**
     * RegisterController constructor.
     * @param RegisterService $registerService
     * @param CredentialResponseBuilderService $credentialResponseBuilderService
     */
    public function __construct(RegisterService $registerService, CredentialResponseBuilderService $credentialResponseBuilderService){
        $this->registerService = $registerService;
        $this->credentialResponseBuilderService = $credentialResponseBuilderService;
    }

    /**
     * <p>This is the json body for register request.</p>
     * <pre> {"email" : "example@gmail.com", "plainPassword" : "******" } </pre>
     *
     * @ApiDoc(
     *  resource=true,
     *  description="This is for registering the user",
     *  section="Users"
     * )
     *
     * @REST\View()
     * @REST\Post(path="users")
     *
     * @param Request $request
     *
     * @return JsonResponse|FormInterface
     */
    public function registerAction(Request $request)
    {
        $form = $this->createForm(RegisterType::class);

        $form->submit($request->request->all());

        if ($form->isSubmitted() && $form->isValid()) {

            $user = $this->registerService->registerUser($form->getData());

            return $this->credentialResponseBuilderService->createCredentialResponse($user);
        }

        return $form;
    }
}