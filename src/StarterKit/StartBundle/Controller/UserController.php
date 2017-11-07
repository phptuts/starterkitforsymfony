<?php

namespace StarterKit\StartBundle\Controller;

use StarterKit\StartBundle\Entity\BaseUser;
use StarterKit\StartBundle\Form\ChangePasswordType;
use StarterKit\StartBundle\Form\ForgetPasswordType;
use StarterKit\StartBundle\Form\RegisterType;
use StarterKit\StartBundle\Form\ResetPasswordType;
use StarterKit\StartBundle\Form\UpdateUserType;
use StarterKit\StartBundle\Form\UserImageType;
use StarterKit\StartBundle\Security\Voter\UserVoter;
use StarterKit\StartBundle\Service\AuthResponseService;
use StarterKit\StartBundle\Service\FormSerializer;
use StarterKit\StartBundle\Service\S3Service;
use StarterKit\StartBundle\Service\UserService;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;
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
     * @var S3Service
     */
    protected $s3Service;

    /**
     * UserController constructor.
     * @param FormSerializer $formSerializer
     * @param UserService $userService
     * @param AuthResponseService $credentialResponseService
     * @param S3Service $s3Service
     */
    public function __construct(FormSerializer $formSerializer,
                                UserService $userService,
                                AuthResponseService $credentialResponseService,
                                S3Service $s3Service)
    {
        parent::__construct($formSerializer);
        $this->userService = $userService;
        $this->authResponseService = $credentialResponseService;
        $this->s3Service = $s3Service;
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
     * @param integer $id
     *
     * @Route(path="/users/{id}", methods={"PATCH"})
     *
     * @return FormInterface|Response
     */
    public function updateUserAction(Request $request, $id)
    {
        $user = $this->getUserById($id);

        $form = $this->createForm(UpdateUserType::class, $user);

        $form->submit($request->request->all());


        if ($form->isSubmitted() && $form->isValid()) {

            /** @var BaseUser $user */
            $user = $form->getData();

            $this->userService->save($user);

            return $this->serializeSingleObject($user->singleView(), BaseUser::RESPONSE_TYPE,  Response::HTTP_OK);
        }

        return $this->serializeFormError($form);
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

        return $this->serializeFormError($form);
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

        return $this->serializeFormError($form);
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
     *
     * @param Request $request
     * @param integer $id
     *
     * @return FormInterface|Response
     */
    public function changePasswordAction(Request $request, $id)
    {
        $user = $this->getUserById($id);

        $form = $this->createForm(ChangePasswordType::class);

        $form->submit($request->request->all());

        if ($form->isSubmitted() && $form->isValid()) {

            $user->setPlainPassword($form->get('newPassword')->getData());
            $this->userService->saveUserWithPlainPassword($user);

            return new Response('', Response::HTTP_NO_CONTENT);
        }

        return $this->serializeFormError($form);
    }

    /**
     * @Security("has_role('ROLE_USER')")
     *
     * @ApiDoc(
     *  resource=true,
     *  description="Update the image for a user",
     *  section="Users",
     *  authentication=true,
     *  parameters={
     *      {
     *          "name"="image",
     *          "dataType"="file",
     *          "required"=true,
     *          "description"="The image profile image it can only be jpg, gif, png."
     *      }
     *  }
     *  )
     * @Route(path="/users/{id}/image", methods={"POST"})
     *
     * @param Request $request
     * @param integer $id
     *
     * @return Response
     */
    public function imageAction(Request $request, $id)
    {
        $user = $this->getUserById($id);
        $form = $this->createForm(UserImageType::class, $user);

        $form->submit(['image' => $request->files->get('image')]);

        if ($form->isSubmitted() && $form->isValid()) {
            $url = $this->s3Service->uploadFile(
                $user->getImage(),
                'profile_pics',
                md5($user->getId() . '_profile_id')
            );
            $user->setImageUrl($url);
            $this->userService->save($user);

            return new Response('', Response::HTTP_NO_CONTENT);
        }

        return $this->serializeFormError($form);
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
     * @param integer $id
     * @Route(path="/users/{id}", methods={"GET"})
     *
     * @return Response
     */
    public function getUserAction($id)
    {
        $user = $this->getUserById($id);
        $this->denyAccessUnlessGranted(UserVoter::USER_CAN_VIEW_EDIT, $user);

        return $this->serializeSingleObject($user->singleView(), BaseUser::RESPONSE_TYPE);
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

        return $this->serializeList($users, BaseUser::RESPONSE_TYPE, $page);
    }

    /**
     * Gets the user by the user's id
     *
     * @param $id
     * @return null|object|BaseUser
     */
    private function getUserById($id)
    {
        $user = $this->userService->findUserById($id);

        if (empty($user)) {
            throw $this->createNotFoundException('user not found');
        }

        return $user;
    }

}