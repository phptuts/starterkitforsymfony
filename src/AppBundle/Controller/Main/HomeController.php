<?php

namespace AppBundle\Controller\Main;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use StarterKit\StartBundle\Exception\ProgrammerException;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class HomeController
 * @package AppBundle\Controller
 */
class HomeController extends Controller
{

    /**
     * @Route("/", name="homepage")
     *
     * @return Response
     */
    public function indexAction()
    {
        return $this->render('@App/main/home/index.html.twig');
    }

    /**
     * @Route("/exception", name="exception_example")
     *
     * @throws ProgrammerException
     */
    public function exceptionAction()
    {
        throw new ProgrammerException('Silly Exception', ProgrammerException::STUPID_EXCEPTION);
    }

    /**
     * @Route("/privacy-policy", name="privacy-policy")
     *
     * @return Response
     */
    public function privacyPolicyAction()
    {
        return $this->render('@App/main/home/privacy-policy.html.twig');
    }
}
