<?php

namespace AppBundle\Controller;

use CoreBundle\Entity\Color;
use CoreBundle\Exception\ProgrammerException;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
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
        return $this->render('@App/home/index.html.twig');
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

}
