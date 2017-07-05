<?php


namespace AppBundle\Controller;


use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class SecurityController extends Controller
{

    /**
     * @Route(name="facebook_login_check", path="facebook_login_check", methods={"POST"})
     * @param Request $request
     * @return Response
     */
    public function facebookSecurityAction(Request $request)
    {
        // Because anonymous is allowed it will not pass through the start end point if authentication fails
        // This is why we forward it here to start function in the guard which is designed to handle this.
        return $this->get('startsymfony.core.session_facebook_guard')->start($request);
    }

    /**
     * @Route(name="google_login_check", path="google_login_check", methods={"POST"})
     * @param Request $request
     * @return Response
     */
    public function googleSecurityAction(Request $request)
    {
        // Because anonymous is allowed it will not pass through the start end point if authentication fails
        // This is why we forward it here to start function in the guard which is designed to handle this.
        return $this->get('startsymfony.core.session_google_guard')->start($request);
    }
}