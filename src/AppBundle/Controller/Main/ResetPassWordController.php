<?php

namespace AppBundle\Controller\Main;

use AppBundle\Form\User\ResetPasswordType;
use AppBundle\Service\User\UserService;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class UserController
 * @package AppBundle\Controller
 */
class ResetPassWordController extends Controller
{
    /**
     * @var UserService
     */
    private $userService;

    public function __construct(UserService $userService)
    {
        $this->userService = $userService;
    }

    /**
     * @param Request $request
     * @param string $token
     *
     * @Route("/reset-password/{token}", name="reset_password")
     *
     * @return Response
     */
    public function resetPasswordAction(Request $request, $token)
    {
        $user = $this->userService->findUserByForgetPasswordToken(urldecode($token));

        if (empty($user)) {

            return $this->render('@App/main/reset-password/reset-password-invalid-token.html.twig');
        }

        $form = $this->createForm(ResetPasswordType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->userService->saveUserForResetPassword($user);

            return $this->redirectToRoute('reset_password_success');
        }

        return $this->render('@App/main/reset-password/reset-password.html.twig', [
            'resetPasswordForm' => $form->createView(),
        ]);
    }

    /**
     *
     * @Route("/reset-password-success", name="reset_password_success")
     * @return Response
     */
    public function resetPasswordSuccessAction()
    {
        return $this->render('@App/main/reset-password/reset-password-success.html.twig');
    }

}