<?php


namespace AppBundle\Controller\Api;

use FOS\RestBundle\Controller\Annotations as REST;
use FOS\RestBundle\Controller\FOSRestController;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;

/**
 * Class UserController
 * @package ApiBundle\Controller\Api
 * @REST\NamePrefix("api_users_")
 */
class LoginController extends FOSRestController
{

    /**
     *  This is an example of a facebook user logging in the with a token
     *  <pre> {"type" : "facebook", "token" : "sdfasdfasdfasdf" } </pre>
     *
     *  This is an example of a user using a refresh token
     *  <pre> {"type" : "refresh_token", "token" : "sdfasdfasdfasdf" } </pre>
     *
     *  This is an example of a user logging in with email and password
     *  <pre> {"email" : "example@gmail.com", "password" : "*******" } </pre>
     *
     * @REST\Post(path="login_check", name="_api_doc_login_check")
     *
     * @ApiDoc(
     *  resource=true,
     *  description="Api Login End Point",
     *  section="Security"
     * )
     *
     */
    public function loginAction()
    {
        throw new \LogicException("Should never hit this end point symfony should take this over.");
    }



}