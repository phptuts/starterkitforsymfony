<?php

namespace StarterKit\StartBundle\Controller;

use StarterKit\StartBundle\Form\ChangePasswordType;
use StarterKit\StartBundle\Form\ForgetPasswordType;
use StarterKit\StartBundle\Form\RegisterType;
use StarterKit\StartBundle\Form\ResetPasswordType;
use StarterKit\StartBundle\Form\UpdateUserType;
use StarterKit\StartBundle\Security\Voter\UserVoter;
use StarterKit\StartBundle\Service\AuthResponseService;
use StarterKit\StartBundle\Service\FormSerializer;
use StarterKit\StartBundle\Service\UserService;
use StarterKit\StartBundle\Tests\Entity\User;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class UserController
 * @package ApiBundle\Controller\Api
 */
class UserController extends BaseRestController
{
    /**
     * @var UserService
     */
    protected $userService;

    /**
     * @var AuthResponseService
     */
    protected $authResponseService;

    /**
     * @var FormSerializer
     */
    protected $formSerializer;

    /**
     * UserController constructor.
     * @param FormSerializer $formSerializer
     * @param UserService $userService
     * @param AuthResponseService $credentialResponseService
     */
    public function __construct(FormSerializer $formSerializer,
                                UserService $userService,
                                AuthResponseService $credentialResponseService)
    {
        parent::__construct($formSerializer);
        $this->userService = $userService;
        $this->authResponseService = $credentialResponseService;
    }

    /**
     *  This is an example of a facebook user logging in the with a token
     *  <pre> {"type" : "facebook", "token" : "sdfasdfasdfasdf" } </pre>
     *
     *  This is an example of a user using a refresh token
     *  <pre> {"type" : "refresh_token", "token" : "sdfasdfasdfasdf" } </pre>
     *
     *  This is an example of a user logging in with email and password
     *  <pre> {"email" : "example@gmail.com", "password" : "*******" } </pre>
     *
     * @Route(path="login_check", name="_api_doc_login_check", methods={"POST"})
     *
     * @ApiDoc(
     *  resource=true,
     *  description="Api Login End Point",
     *  section="Security"
     * )
     *
     */
    public function loginAction()
    {
        throw new \LogicException("Should never hit this end point symfony should take this over.");
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
     * @Route(path="/users", methods={"POST"})
     *
     * @param Request $request
     *
     * @return JsonResponse
     */
    public function registerAction(Request $request)
    {
        $form = $this->createForm(RegisterType::class);

        $form->submit($request->request->all());

        if ($form->isSubmitted() && $form->isValid()) {

            $user = $this->userService->registerUser($form->getData());

            return $this->authResponseService->createAuthResponse($user);
        }

        return $this->serializeFormError($form);
    }

    /**
     * @Security("has_role('ROLE_USER')")
     *
     * <p>This updates the user.  Whatever user field you have.</pre>
     * <pre> {"displayName": "jo32", "email": "example@sdf.com" }</pre>
     *
     * @ApiDoc(
     *  resource=true,
     *  description="Update's the user",
     *  section="Users",
     *  authentication=true
     * )
     *
     * @param Request $request
     * @param User $user
     *
     * @Route(path="users/{id}", methods={"PATCH"})
     * @ParamConverter(name="user", class="StarterKit\StartBundle:User")
     *
     * @return FormInterface|Response
     */
    public function updateUserAction(Request $request, User $user)
    {
        $form = $this->createForm(UpdateUserType::class, $user);

        $form->submit($request->request->all());


        if ($form->isSubmitted() && $form->isValid()) {

            $user = $form->getData();

            $this->userService->save($user);

            return $this->serializeSingleObject($user,  Response::HTTP_OK);
        }

        return $form;
    }

    /**
     * <p>This is the json body for forget passwor request.</p>
     * <pre> {"email" : "example@gmail.com" } </pre>
     *
     * @ApiDoc(
     *  resource=true,
     *  description="For creating a forget password email and token",
     *  section="Users"
     * )
     *
     * @Route(path="/users/forget-password", methods={"POST"})
     *
     * @param Request $request
     *
     * @return \Symfony\Component\Form\Form|Response
     */
    public function forgetPasswordAction(Request $request)
    {
        $form = $this->createForm(ForgetPasswordType::class);

        $form->submit($request->request->all());

        if ($form->isSubmitted() && $form->isValid()) {
            $this->userService->forgetPassword($form->getData());

            return new Response('', Response::HTTP_NO_CONTENT);
        }

        return $form;
    }

    /**
     * <p>This is the json body for resetting the password using a forget password token.</p>
     * <pre> {"plainPassword" : "******" } </pre>
     *
     * @ApiDoc(
     *  resource=true,
     *  description="For reset's the user's password using a reset password token",
     *  section="Users"
     * )
     * @Route(path="/users/reset-password/{token}", methods={"PATCH"})
     *
     * @param Request $request
     * @param string $token
     *
     * @return Response|FormInterface
     */
    public function resetPasswordAction(Request $request, $token)
    {
        $user = $this->userService->findUserByForgetPasswordToken(urldecode($token));

        if (empty($user)) {

            throw $this->createNotFoundException('Invalid Token');
        }

        $form = $this->createForm(ResetPasswordType::class, $user);
        $form->submit($request->request->all());

        if ($form->isSubmitted() && $form->isValid()) {
            $this->userService->saveUserForResetPassword($user);

            return new Response('', Response::HTTP_NO_CONTENT);
        }

        return $form;
    }


    /**
     * @Security("has_role('ROLE_USER')")
     *
     * <p>If the user is not an admin they are required to enter their current password.</p>
     * <pre> {"newPassword": "*****", "currentPassword": "****" }</pre>
     *
     * <p>If the user is an admin</pre>
     * <pre> {"newPassword": "*****" }</pre>
     *
     * @ApiDoc(
     *  resource=true,
     *  description="Changes the user's password",
     *  section="Users",
     *  authentication=true
     * )
     *
     * @Route(path="/users/{id}/password", methods={"PATCH"})
     *
     * @ParamConverter(name="user", class="StarterKit\StartBundle:User")
     *
     * @param Request $request
     * @param User $user
     *
     * @return FormInterface|Response
     */
    public function changePasswordAction(Request $request, User $user)
    {
        $form = $this->createForm(ChangePasswordType::class);

        $form->submit($request->request->all());

        if ($form->isSubmitted() && $form->isValid()) {

            $user->setPlainPassword($form->get('newPassword')->getData());
            $this->userService->saveUserWithPlainPassword($user);

            return new Response('', Response::HTTP_NO_CONTENT);
        }

        return $form;
    }


    /**
     * @Security("has_role('ROLE_USER')")
     *
     * @ApiDoc(
     *  resource=true,
     *  description="Get's a user",
     *  section="Users",
     *  authentication=true
     * )
     *
     * @Route(path="/users/{id}", methods={"GET"})
     *
     * @ParamConverter(name="user", class="StarterKit\StartBundle:User")
     *
     * @param User $user
     *
     * @return Response
     */
    public function getUserAction(User $user)
    {
        $this->denyAccessUnlessGranted(UserVoter::USER_CAN_VIEW_EDIT, $user);

        return $this->serializeSingleObject($user);
    }

    /**
     * @Security("has_role('ROLE_ADMIN')")
     *
     * @ApiDoc(
     *  resource=true,
     *  description="Get's a list of users, admin only.",
     *  section="Users",
     *  authentication=true
     * )
     *
     * @Route(path="/users", methods={"GET"})
     *
     * @param Request $request
     *
     * @return Response
     */
    public function getUsersAction(Request $request)
    {
        $page = $request->query->get('page', 1);

        $users = $this->userService->searchUser(
                $request->query->get('q'),
                $page
            );

        return $this->serializeList($users, 'users', $page);
    }
}