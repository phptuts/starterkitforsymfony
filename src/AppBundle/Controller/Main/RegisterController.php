<?php

namespace AppBundle\Controller\Main;

use AppBundle\Form\User\RegisterType;
use AppBundle\Service\User\RegisterService;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;
use Symfony\Component\Security\Guard\GuardAuthenticatorHandler;

class RegisterController extends Controller
{
    /**
     * @var GuardAuthenticatorHandler
     */
    private $authenticatorHandler;

    /**
     * @var RegisterService
     */
    private $registerService;

    public function __construct(RegisterService $registerService, GuardAuthenticatorHandler $authenticatorHandler)
    {
        $this->authenticatorHandler = $authenticatorHandler;
        $this->registerService = $registerService;
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
            $user = $this->registerService->registerUser($form->getData());

            // Be sure your firewall name is main or matches the what is in your security.yml file.
            // We don't do this in the service because the request is required
            $this->authenticatorHandler
                ->authenticateWithToken(new UsernamePasswordToken($user, null, 'main', $user->getRoles()), $request);

            // This is a hidden form value in the twig.  It does not bind to the form.
            // This will not work for certain browsers like firefox or browser that don't do the referer.
            // Redirects to the previous page. If the page does not have the referer it will redirect to the home page.
            return new RedirectResponse($request->request->get('redirect_url'));
        }

        return $this->render('@App/main/register/register.html.twig', [
            'registerForm' => $form->createView()
        ]);
    }
}