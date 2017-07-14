<?php

namespace ApiBundle\Controller;


use CoreBundle\Form\User\ChangePasswordType;
use CoreBundle\Form\User\ForgetPasswordType;
use CoreBundle\Form\User\RegisterType;
use CoreBundle\Form\User\ResetPasswordType;
use CoreBundle\Form\User\UpdateUserType;
use CoreBundle\Form\User\UserImageType;
use CoreBundle\Security\Voter\UserVoter;
use FOS\RestBundle\Controller\Annotations as REST;
use CoreBundle\Entity\User;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * TODO API TESTS
 * TODO NELMIO API DOC BUNDLE
 * Class UserController
 * @package ApiBundle\Controller
 * @REST\NamePrefix("api_users_")
 */
class UserController extends AbstractRestController
{

    /**
     * @REST\View()
     *
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

            $user = $this->get('startsymfony.core.register_service')->registerUser($form->getData());

            return $this->get('startsymfony.core.credential_response_builder')->createCredentialResponse($user);
        }

        return $form;
    }

    /**
     * @REST\View()
     *
     * @REST\Post(path="/users/forget-password")
     *
     * @param Request $request
     * @return \Symfony\Component\Form\Form|Response
     */
    public function forgetPasswordAction(Request $request)
    {
        $form = $this->createForm(ForgetPasswordType::class);

        $form->submit($request->request->all());

        if ($form->isSubmitted() && $form->isValid()) {
            $this->get('startsymfony.core.forget_password')->forgetPassword($form->getData());

            return new Response('', Response::HTTP_NO_CONTENT);
        }

        return $form;
    }

    /**
     * @REST\View()
     *
     * @param Request $request
     * @param string $token
     *
     * @REST\Patch("users/reset-password/{token}")
     *
     * @return Response|FormInterface
     */
    public function resetPasswordAction(Request $request, $token)
    {
        $user = $this->get('startsymfony.core.repository.user_repository')->findUserByForgetPasswordToken(urldecode($token));

        if (empty($user)) {

            throw $this->createNotFoundException('Invalid Token');
        }

        $form = $this->createForm(ResetPasswordType::class, $user);
        $form->submit($request->request->all());

        if ($form->isSubmitted() && $form->isValid()) {
            $this->get('startsymfony.core.user_service')->saveUserForResetPassword($user);

            return new Response('', Response::HTTP_NO_CONTENT);
        }

        return $form;
    }

    /**
     *
     * @param Request $request
     * @param User $user
     *
     * @REST\Post("users/{id}/image")
     * @ParamConverter(name="user", class="CoreBundle:User")
     *
     * @return Response|FormInterface
     */
    public function imageAction(Request $request, User $user)
    {
        $form = $this->createForm(UserImageType::class, $user);

        $form->submit(['image' => $request->files->get('image')]);

        if ($form->isSubmitted() && $form->isValid()) {
            $url = $this->get('startsymfony.core.s3_service')
                ->uploadFile($user->getImage(), 'profile_pics', md5($user->getId() . '_profile_id'));
            $user->setImageUrl($url);
            $this->get('startsymfony.core.user_service')->save($user);

            return new Response('', Response::HTTP_NO_CONTENT);
        }

        return $form;
    }

    /**
     * @REST\View()
     *
     * @param Request $request
     * @param User $user
     *
     * @REST\Post(path="users/{id}/password")
     * @ParamConverter(name="user", class="CoreBundle:User")
     *
     * @return FormInterface|Response
     */
    public function changePasswordAction(Request $request, User $user)
    {
        $form = $this->createForm(ChangePasswordType::class);

        $form->submit($request->request->all());

        if ($form->isSubmitted() && $form->isValid()) {

            $user->setPlainPassword($form->get('newPassword')->getData());
            $this->get('startsymfony.core.user_service')->saveUserWithPlainPassword($user);

            return new Response('', Response::HTTP_NO_CONTENT);
        }

        return $form;
    }


    /**
     * @REST\View()
     *
     * @param Request $request
     * @param User $user
     *
     * @REST\Post(path="users/{id}")
     * @ParamConverter(name="user", class="CoreBundle:User")
     *
     * @return FormInterface|Response
     */
    public function updateUserAction(Request $request, User $user)
    {
        $form = $this->createForm(UpdateUserType::class, $user, ['api' => true]);

        $form->submit($request->request->all());


        if ($form->isSubmitted() && $form->isValid()) {

            $user = $form->getData();

            $this->get('startsymfony.core.user_service')->save($user);

            return $this->serializeSingleObject($user, [User::USER_PERSONAL_SERIALIZATION_GROUP], Response::HTTP_OK);
        }

        return $form;
    }


    /**
     * @REST\View()
     *
     * @REST\Get(path="users/{id}")
     *
     * @ParamConverter(name="user", class="CoreBundle:User")
     *
     * @param User $user
     *
     * @return Response
     */
    public function getUserAction(User $user)
    {
        $this->denyAccessUnlessGranted(UserVoter::USER_CAN_VIEW_EDIT, $user);

        return $this->serializeSingleObject($user, [User::USER_PERSONAL_SERIALIZATION_GROUP], Response::HTTP_OK);
    }

    /**
     *
     * @Security("has_role('ROLE_ADMIN')")
     * @REST\View()
     * @REST\Get(path="users")
     * @REST\QueryParam(name="q", description="The search query", nullable=true)
     * @REST\QueryParam(name="page", description="The current page ", nullable=true)
     * @param Request $request
     *
     * @return Response
     */
    public function getUsersAction(Request $request)
    {
        $page = $request->query->get('page', 1);

        $users = $this
            ->get('startsymfony.core.repository.user_repository')
            ->getUsers(
                $request->query->get('q'),
                $page
            );

        return $this->serializeList($users, 'users', $page, [User::USER_PERSONAL_SERIALIZATION_GROUP], Response::HTTP_OK);
    }

}