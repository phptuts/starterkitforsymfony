<?php

namespace StarterKit\StartBundle\Controller;

use Symfony\Component\Routing\Annotation\Route;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;

class SecurityController
{
    /**
     *
     *  This is an example of a facebook user logging in the with a token
     *  <pre> {"type" : "facebook", "token" : "sdfasdfasdfasdf" } </pre>
     *
     *  This is an example of a user using a refresh token
     *  <pre> {"type" : "refresh_token", "token" : "sdfasdfasdfasdf" } </pre>
     *
     *  This is an example of a user logging in with email and password
     *  <pre> {"email" : "example@gmail.com", "password" : "*******" } </pre>
     *
     * @ApiDoc(
     *  resource=true,
     *  description="Api Login End Point",
     *  section="Security"
     * )
     *
     * @Route(path="login_check", name="_api_doc_login_check", methods={"POST"})
     *
     */
    public function loginAction()
    {
        throw new \LogicException("Should never hit this end point symfony should take this over.");
    }
}