<?php

namespace Tests\AppBundle\Controller;

use Liip\FunctionalTestBundle\Test\WebTestCase;
use PHPUnit\Framework\Assert;

class UserControllerTest extends WebTestCase
{
    const EXAMPLE_USER_EMAIL = 'example_user@gmail.com';

    /**
     * This tests that a user can register
     */
    public function testRegisterPage()
    {
        $client = $this->makeClient();
        $crawler = $client->request('GET', '/register');
        $this->assertStatusCode(200, $client);

        $form = $crawler->selectButton('Register')->form();
        $crawler = $client->submit($form);

        $this->assertStatusCode(200, $client);
        $this->assertValidationErrors(['data.email', 'data.plainPassword'], $client->getContainer());

        $form = $crawler->selectButton('Register')->form();
        $form->setValues(['register[email]' => self::EXAMPLE_USER_EMAIL, 'register[plainPassword]' => 'password']);
        $client->submit($form);

        $this->assertStatusCode(302, $client);
    }

    /**
     * 1) Tests that if nothing is enter a validation message appers
     * 2) Tests that a user can login and that they can visit a secure page
     *
     * @depends testRegisterPage
     */
    public function testLoginPage()
    {
        $client = $this->makeClient();
        $crawler = $client->request('GET', '/login');
        $this->assertStatusCode(200, $client);
        $form = $crawler->selectButton('Login')->form();
        // testing error message shows up after validation
        $client->submit($form);
        $crawler = $client->followRedirect();
        Assert::assertEquals(1, $crawler->filter('#error-message')->count());

        $form = $crawler->selectButton('Login')->form();
        $form->setValues(['_username' => self::EXAMPLE_USER_EMAIL, '_password' => 'password']);
        $client->submit($form);
        $client->followRedirect();

        // This is testing that the user can login to a secure area which is account settings
        $client->request('GET', '/account-settings/information');
        $this->assertStatusCode(200, $client);
    }

    /**
     * 1) Tests that email address is required
     * 2) Tests that if an email is not in our database an error is shown
     * 3) Tests that if the email exists that the user is redirected
     * 4) Tests the redirect page
     * @depends testRegisterPage
     */
    public function testForgetPassword()
    {
        // Navigating to forget password page
        $client = $this->makeClient();
        $crawler = $client->request('GET', '/forget-password');
        $this->assertStatusCode(200, $client);

        // Submitting form and testing the error show up
        $form = $crawler->selectButton('Forget Password')->form();
        $crawler = $client->submit($form);
        $this->assertStatusCode(200, $client);
        $this->assertValidationErrors(['data.email'], $client->getContainer());

        // Submitting form with email that does not exist in our system and testing that the form shows an error
        $form = $crawler->selectButton('Forget Password')->form();
        $form->setValues(['forget_password[email]' => 'email_does_not@exists.com']);
        $crawler = $client->submit($form);
        $this->assertStatusCode(200, $client);
        // Because this attaches to the form in a weird way we have to to look at the css and make sure that it validates
        Assert::assertEquals(1, $crawler->filter('.has-error')->count());

        // Testing form submit with a valid email address
        $form = $crawler->selectButton('Forget Password')->form();
        $form->setValues(['forget_password[email]' => self::EXAMPLE_USER_EMAIL]);
        $client->submit($form);
        $this->assertStatusCode(302, $client);

        // Testing after reset page works
        $client->request('GET','/forget-password-success');
        $this->assertStatusCode(200, $client);

    }

    /**
     * 1) Test what happens if a bad token is entered
     * 2) Tests a valid token can access the reset password form
     * 3) Tests if a password that is too short is enter that validation appers
     * 4) Tests that if a valid password is enter the user is redirected
     * @depends testForgetPassword
     */
    public function testResetPassword()
    {
        $client = $this->makeClient();
        // Asserting that the form does not exist for bad tokens
        $crawler = $client->request('GET', '/reset-password/bad_token');
        $this->assertStatusCode(200, $client);
        Assert::assertEquals(0,$crawler->selectButton('Reset Password')->count());


        $user = $this->getContainer()
            ->get('startsymfony.core.repository.user_repository')
            ->findUserByEmail(self::EXAMPLE_USER_EMAIL);

        // Going to the forget password page to
        $crawler = $client->request('GET', '/reset-password/' . twig_urlencode_filter($user->getForgetPasswordToken()));
        $this->assertStatusCode(200, $client);
        $form = $crawler->selectButton('Reset Password')->form();

        // Submitting password that should be too short and testing validation
        $form->setValues(['reset_password[plainPassword]' => 'sd']);
        $client->submit($form);
        $this->assertStatusCode(200, $client);
        $this->assertValidationErrors(['data.plainPassword'], $client->getContainer());

        // Submitting valid password
        $form->setValues(['reset_password[plainPassword]' => 'new_password']);
        $client->submit($form);
        $this->assertStatusCode(302, $client);

        // Testing that the redirect page works
        $client->request('GET', '/reset-password-success');
        $this->assertStatusCode(200, $client);

    }

    /**
     * Test that a user can login with the new password and can access a secure area.
     * @depends testResetPassword
     */
    public function testLoginAfterResetPassword()
    {
        $client = $this->makeClient();
        $crawler = $client->request('GET', '/login');
        $this->assertStatusCode(200, $client);
        $form = $crawler->selectButton('Login')->form();
        $form->setValues(['_username' => self::EXAMPLE_USER_EMAIL, '_password' => 'new_password']);
        $client->submit($form);
        $client->followRedirect();

        // This is testing that the user can login to a secure area which is account settings
        $client->request('GET', '/account-settings/information');
        $this->assertStatusCode(200, $client);
    }
}