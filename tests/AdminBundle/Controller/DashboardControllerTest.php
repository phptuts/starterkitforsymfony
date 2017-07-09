<?php

namespace Tests\AdminBundle\Controller;

use Liip\FunctionalTestBundle\Test\WebTestCase;

class DashboardControllerTest extends WebTestCase
{
    /**
     * Tests that account settings login is required
     */
    public function testDashboardAdminRequire()
    {
        $client = $this->makeClient();
        $crawler = $client->request('GET', '/login');
        $form = $crawler->selectButton('Login')->form();
        $form->setValues(['_username' => 'regular_user@gmail.com', '_password' => 'password']);
        $client->submit($form);


        $client->request('GET', '/admin/dashboard');
        $this->assertStatusCode(403, $client);

        $client->request('GET', '/logout');


        $crawler = $client->request('GET', '/login');
        $form = $crawler->selectButton('Login')->form();
        $form->setValues(['_username' => 'admin_user@gmail.com', '_password' => 'password']);
        $client->submit($form);

        $client->request('GET', '/admin/dashboard');
        $this->assertStatusCode(200, $client);

    }
}