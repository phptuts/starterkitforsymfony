<?php


namespace Tests\AppBundle\Controller\Main;

use Liip\FunctionalTestBundle\Test\WebTestCase;

class HomeControllerTest extends WebTestCase
{
    /**
     * Testing that the home page loads
     */
    public function testHomePageAndChangeColorPage()
    {
        $client = $this->makeClient();
        $client->request('GET', '/');
        $this->assertStatusCode(200, $client);
    }
}