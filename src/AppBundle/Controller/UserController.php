<?php

namespace AppBundle\Controller;

use CoreBundle\Form\User\ForgetPasswordType;
use CoreBundle\Form\User\RegisterType;
use CoreBundle\Form\User\ResetPasswordType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;

/**
 * Class UserController
 * @package AppBundle\Controller
 */
class UserController extends Controller
{

    /**
     * @Route("/login", name="login")
     *
     * @return Response
     */
    public function loginAction()
    {
        $authenticationUtils = $this->get('security.authentication_utils');

        // get the login error if there is one
        $error = $authenticationUtils->getLastAuthenticationError();

        // last username entered by the user
        $lastUsername = $authenticationUtils->getLastUsername();

        return $this->render('@App/user/login.html.twig', [
            'last_username' => $lastUsername,
            'error' => $error,
        ]);
    }

    /**
     * @Route("/register", name="register")
     * @param  Request $request
     * @return Response
     */
    public function registerAction(Request $request)
    {

        $form = $this->createForm(RegisterType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $user = $this->get('startsymfony.core.register_service')->registerUser($form->getData());

            // Be sure your firewall name is main or matches the what is in your security.yml file.
            // We don't do this in the service because the request is required
            $this->get('security.authentication.guard_handler')
                    ->authenticateWithToken(new UsernamePasswordToken($user, null, 'main', $user->getRoles()), $request);

            // This is a hidden form value in the twig.  It does not bind to the form.
            // This will not work for certain browsers like firefox or browser that don't do the referer.
            if ($request->request->has('redirect_url')) {
                // Redirects to the previous page
                return new RedirectResponse($request->request->get('redirect_url'));
            }

            return $this->redirectToRoute('homepage');
        }

        return $this->render('@App/user/register.html.twig', [
            'registerForm' => $form->createView()
        ]);
    }


    /**
     * @param Request $request
     * @Route("/forget-password", name="forget_password")
     *
     * @return Response
     */
    public function forgetPasswordAction(Request $request)
    {

        $form = $this->createForm(ForgetPasswordType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->get('startsymfony.core.forget_password')->forgetPassword($form->getData());

            return $this->redirectToRoute('forget_password_success');
        }

        return $this->render('@App/user/forget-password/forget-password.html.twig', [
            'forgetPasswordForm' => $form->createView()
        ]);
    }

    /**
     * @Route("/forget-password-success", name="forget_password_success")
     *
     * @return Response
     */
    public function forgetPasswordSuccessAction()
    {
        return $this->render('@App/user/forget-password/forget-password-success.html.twig');
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
        $user = $this->get('startsymfony.core.repository.user_repository')->findUserByForgetPasswordToken(urldecode($token));

        if (empty($user)) {

            return $this->render('@App/user/reset-password/reset-password-invalid-token.html.twig');
        }

        $form = $this->createForm(ResetPasswordType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->get('startsymfony.core.user_service')->saveUserForResetPassword($user);

            return $this->redirectToRoute('reset_password_success');
        }

        return $this->render('@App/user/reset-password/reset-password.html.twig', [
            'resetPasswordForm' => $form->createView()
        ]);
    }

    /**
     *
     * @Route("/reset-password-success", name="reset_password_success")
     * @return Response
     */
    public function resetPasswordSuccessAction()
    {
        return $this->render('@App/user/reset-password/reset-password-success.html.twig');
    }

}