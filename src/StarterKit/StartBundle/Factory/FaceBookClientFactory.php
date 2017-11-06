<?php


namespace StarterKit\StartBundle\Factory;


use Facebook\Facebook;

/**
 * Class FaceBookClientFactory
 * @package StarterKit\StartBundle\Factory
 */
class FaceBookClientFactory
{
    /**
     * @var Facebook
     */
    protected $fb;

    /**
     * FaceBookClientFactory constructor.
     * @param $appId
     * @param $appSecret
     * @param $apiVersion
     */
    public function __construct($appId, $appSecret, $apiVersion)
    {
        $this->fb = new Facebook([
            'app_id' => $appId, // Replace {app-id} with your app id
            'app_secret' => $appSecret,
            'default_graph_version' => $apiVersion,
            'http_client_handler' => 'curl'
        ]);
    }

    /**
     * @return Facebook
     */
    public function getFacebookClient()
    {
        return $this->fb;
    }
}