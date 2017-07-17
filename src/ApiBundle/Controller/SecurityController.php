<?php


namespace ApiBundle\Controller;

use CoreBundle\Exception\ProgrammerException;
use FOS\RestBundle\Controller\Annotations as REST;
use FOS\RestBundle\Controller\FOSRestController;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;

class SecurityController extends FOSRestController
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

    /**
     * @REST\Get(path="fake_facebook_token")
     *
     * @ApiDoc(
     *  resource=true,
     *  description="Get's a fake facebook token",
     *  section="Security"
     * )
     *
     */
    public function getFakeFacebookTokenAction()
    {
        $url = 'https://graph.facebook.com/oauth/access_token?client_id=' . $this->getParameter('facebook_app_id')
            . '&client_secret=' . $this->getParameter('facebook_app_secret') . '&grant_type=client_credentials&redirect_uri=http://skfsp.info&permissions=email';

        $data = json_decode(file_get_contents($url), true);

        $facebookClient = $this->get('startsymfony.core.facebook_client_factory')->getFacebookClient();

        $response = $facebookClient->get('/'. $this->getParameter('facebook_app_id') . '/accounts/test-users', $data['access_token']);

        $userAccessToken = $response->getDecodedBody()['data'][0]['access_token'];

        $response = $facebookClient->get('/me?fields=email', $userAccessToken);

        return ['email' => $response->getGraphUser()->getEmail(), 'token' => $userAccessToken];
    }

    /**
     * @REST\Get(path="stupid_exception")
     *
     * @ApiDoc(
     *  resource=true,
     *  description="example of an exception",
     *  section="Security"
     * )
     */
    public function testStupidExceptionApiAction()
    {
        throw new ProgrammerException('I am a stupid exception.', ProgrammerException::STUPID_EXCEPTION);
    }
}