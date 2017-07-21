<?php

namespace AppBundle\Controller\Api;

use AppBundle\Entity\User;
use AppBundle\Form\User\ChangePasswordType;
use AppBundle\Form\User\ForgetPasswordType;
use AppBundle\Form\User\ResetPasswordType;
use AppBundle\Service\User\ForgetPasswordService;
use AppBundle\Service\User\UserService;
use FOS\RestBundle\Controller\FOSRestController;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class UserPasswordController extends FOSRestController
{
    /**
     * @var UserService
     */
    private $userService;

    /**
     * @var ForgetPasswordService
     */
    private $forgetPasswordService;

    public function __construct(UserService $userService, ForgetPasswordService $forgetPasswordService)
    {
        $this->userService = $userService;
        $this->forgetPasswordService = $forgetPasswordService;
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
     * @REST\View()
     * @REST\Post(path="/users/forget-password")
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
            $this->forgetPasswordService->forgetPassword($form->getData());

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
     *
     * @REST\View()
     * @REST\Patch("users/reset-password/{token}")
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
     * @REST\View()
     * @REST\Patch(path="users/{id}/password")
     *
     * @ParamConverter(name="user", class="AppBundle:User")
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
}