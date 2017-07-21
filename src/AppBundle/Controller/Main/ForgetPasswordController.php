<?php

namespace AppBundle\Controller\Main;

use AppBundle\Form\User\ForgetPasswordType;
use AppBundle\Service\User\ForgetPasswordService;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class ForgetPasswordController extends Controller
{
    /**
     * @var ForgetPasswordService
     */
    private $forgetPasswordService;

    public function __construct(ForgetPasswordService $forgetPasswordService)
    {
        $this->forgetPasswordService = $forgetPasswordService;
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
            $this->forgetPasswordService->forgetPassword($form->getData());

            return $this->redirectToRoute('forget_password_success');
        }

        return $this->render('@App/main/forget-password/forget-password.html.twig', [
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
        return $this->render('@App/main/forget-password/forget-password-success.html.twig', ['title' => 'Forget Password']);
    }
}