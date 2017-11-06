<?php

namespace StarterKit\StartBundle\Factory;

/**
 * Class GoogleClientFactory
 * @package StarterKit\StartBundle\Factory
 */
class GoogleClientFactory
{
    private $googleClientId;

    /**
     * GoogleClientFactory constructor.
     * @param $googleClientId
     */
    public function __construct($googleClientId)
    {
        $this->googleClientId = $googleClientId;
    }

    /**
     * @return \Google_Client
     */
    public function getGoogleClient()
    {
        return new \Google_Client(['client_id' => $this->googleClientId]);
    }
}