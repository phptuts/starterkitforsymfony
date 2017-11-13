<?php


namespace StarterKit\StartBundle\Factory;


use Facebook\Facebook;

/**
 * Class FaceBookClientFactory
 * @package StarterKit\StartBundle\Factory
 */
class FaceBookClientFactory implements FaceBookClientFactoryInterface
{
    /**
     * @var Facebook
     */
    protected $fb;

    /**
     * FaceBookClientFactory constructor.
     * @param string $appId
     * @param string $appSecret
     * @param string $apiVersion
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
    public function getClient()
    {
        return $this->fb;
    }
}