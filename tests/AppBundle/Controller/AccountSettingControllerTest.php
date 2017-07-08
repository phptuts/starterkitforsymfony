<?php

namespace Tests\AppBundle\Controller;

use Liip\FunctionalTestBundle\Test\WebTestCase;
use PHPUnit\Framework\Assert;

class AccountSettingControllerTest extends WebTestCase
{
    public function testAccountSettingAuthRequired()
    {
        $client = $this->makeClient();
        $client->request('GET', '/account-settings/information');
        $this->assertStatusCode(302, $client);
    }

    /**
     * Test that a user can upload a file and change account setting
     */
    public function testAccountSettingEmailIsPopulated()
    {
        $client = $this->makeClient();
        $crawler = $client->request('GET', '/login');
        $form = $crawler->selectButton('Login')->form();
        $form->setValues(['_username' => 'update_profile@gmail.com', '_password' => 'password']);
        $client->submit($form);


        $crawler = $client->request('GET', '/account-settings/information');
        $this->assertStatusCode(200, $client);
        Assert::assertEquals('update_profile@gmail.com', $crawler->filter('#update_user_email')->first()->attr('value'));

        $form = $crawler->selectButton('Update')->form();

        $form['update_user[display_name]']->setValue('blue_man');
        $form['update_user[bio]']->setValue('this is about me');
        $form['update_user[image]']->upload(__DIR__ .'/cat.png');
        $crawler = $client->submit($form);

        Assert::assertEquals(1, $crawler->filter('#success-flash-message')->count());
        Assert::assertEquals('update_profile@gmail.com', $crawler->filter('#update_user_email')->first()->attr('value'));
        Assert::assertEquals('this is about me', $crawler->filter('#update_user_bio')->first()->text());
        Assert::assertEquals('blue_man', $crawler->filter('#update_user_display_name')->first()->attr('value'));
    }

    /**
     * Tests that a user can change their password and login
     * @depends testAccountSettingEmailIsPopulated
     */
    public function testAccountSettingUpdatePassword()
    {
        // log user in
        $client = $this->makeClient();
        $crawler = $client->request('GET', '/login');
        $form = $crawler->selectButton('Login')->form();
        $form->setValues(['_username' => 'change_password@gmail.com', '_password' => 'password']);
        $client->submit($form);

        // Change the password and make sure the flash message appears
        $crawler = $client->request('GET', '/account-settings/change-password');
        $this->assertStatusCode(200, $client);
        $form = $crawler->selectButton('Change Password')->form();
        $form->get('change_password[current_password]')->setValue('password');
        $form->get('change_password[new_password]')->setValue('new_password');
        $crawler = $client->submit($form);
        Assert::assertEquals(1,$crawler->filter('#success-flash-message')->count());

        // logout user
        $client->request('GET', '/logout');

        // login in user
        $crawler = $client->request('GET', '/login');
        $form = $crawler->selectButton('Login')->form();
        $form->setValues(['_username' => 'change_password@gmail.com', '_password' => 'new_password']);
        $client->submit($form);

        // Go to protected page to make sure user is logged in
        $client->request('GET', '/account-settings/information');
        $this->assertStatusCode(200, $client);
    }
}