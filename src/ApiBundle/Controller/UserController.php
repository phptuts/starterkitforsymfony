<?php

namespace ApiBundle\Controller;


use CoreBundle\Form\User\RegisterType;
use CoreBundle\Model\Response\ResponseModel;
use CoreBundle\Security\Voter\UserVoter;
use FOS\RestBundle\Controller\Annotations as REST;
use FOS\RestBundle\Controller\FOSRestController;
use CoreBundle\Entity\User;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class UserController extends FOSRestController
{

    /**
     * @REST\View()
     *
     * @REST\Post(path="users", name="api_register_users")
     *
     * @param Request $request
     *
     * @return JsonResponse|FormInterface
     */
    public function registerAction(Request $request)
    {
        $form  = $this->createForm(RegisterType::class, null, ['csrf_protection' => false]);

        $form->submit($request->request->all(), true);

        if ($form->isSubmitted() && $form->isValid()) {

            $user = $this->get('startsymfony.core.register_service')->registerUser($form->getData());

            return $this->get('startsymfony.core.credential_response_builder')->createCredentialResponse($user);
        }

        return $form;
    }

    /**
     * @REST\View()
     *
     * @REST\Get(path="users/{id}", name="api_get_users")
     *
     * @ParamConverter(name="user", class="CoreBundle:User")
     *
     * @param User $user
     *
     * @return User
     */
    public function getUserAction(User $user)
    {
       $this->denyAccessUnlessGranted(UserVoter::USER_CAN_VIEW_EDIT, $user);

        return $this
            ->get('startsymfony.core.response')
            ->serializeResponse(
                new ResponseModel($user, ResponseModel::USER_RESPONSE),
                [User::USER_PERSONAL_SERIALIZATION_GROUP],
                Response::HTTP_OK
            );
    }


}