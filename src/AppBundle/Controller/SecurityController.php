<?php


namespace AppBundle\Controller;


use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class SecurityController extends Controller
{

    /**
     * @Security("has_role('ROLE_USER')")
     * @Route(name="social_login_check", path="social_login_check", methods={"POST"})
     * @param Request $request
     * @return Response
     */
    public function socialSecurityAction(Request $request)
    {
        // Because anonymous is allowed it will not pass through the start end point if authentication fails
        // This is why we forward it here to start function in the guard which is designed to handle this.
        return $this->get('startsymfony.core.security.social_guard')->start($request);
    }
}