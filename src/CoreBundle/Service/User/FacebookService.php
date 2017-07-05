<?php


namespace CoreBundle\Service\User;


use CoreBundle\Factory\FaceBookClientFactory;
use Facebook\Facebook;

class FacebookService
{
    /**
     * @var Facebook
     */
    protected $fbClient;

    public function __construct(FaceBookClientFactory $facebookClientFactory)
    {
        $this->fbClient = $facebookClientFactory->getFacebookClient();
    }

    public function getLoginUrl($callBackUrl)
    {
        $helper = $this->fbClient->getRedirectLoginHelper();

        $permissions = ['email', 'public_profile']; // Optional permissions
        return $helper->getLoginUrl($callBackUrl, $permissions);
    }
}