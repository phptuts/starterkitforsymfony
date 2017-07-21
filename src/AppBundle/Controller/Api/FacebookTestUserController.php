<?php

namespace AppBundle\Controller\Api;

use AppBundle\Factory\FaceBookClientFactory;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use FOS\RestBundle\Controller\Annotations as REST;

/**
 * Class UserController
 * @package ApiBundle\Controller\Api
 * @REST\NamePrefix("api_users_")
 */
class FacebookTestUserController extends Controller
{
    /**
     * @var FaceBookClientFactory
     */
    private $faceBookClientFactory;


    public function __construct(FaceBookClientFactory $faceBookClientFactory)
    {
        $this->faceBookClientFactory = $faceBookClientFactory;
    }

    /**
     * Gets a test user's facebook access token.
     *
     * @REST\Get(path="fake_facebook_token")
     */
    public function getFakeFacebookTokenAction()
    {
        if ($this->getParameter('kernel.environment') == 'prod') {
            throw $this->createNotFoundException();
        }

        $url = 'https://graph.facebook.com/oauth/access_token?client_id=' . $this->getParameter('facebook_app_id')
            . '&client_secret=' . $this->getParameter('facebook_app_secret') . '&grant_type=client_credentials&redirect_uri=http://skfsp.info';

        $data = json_decode(file_get_contents($url), true);

        $facebookClient = $this->faceBookClientFactory->getFacebookClient();

        $response = $facebookClient->get('/'. $this->getParameter('facebook_app_id') . '/accounts/test-users', $data['access_token']);

        $userAccessToken = $response->getDecodedBody()['data'][0]['access_token'];

        $response = $facebookClient->get('/me?fields=email', $userAccessToken);

        return ['email' => $response->getGraphUser()->getEmail(), 'token' => $userAccessToken];
    }

}