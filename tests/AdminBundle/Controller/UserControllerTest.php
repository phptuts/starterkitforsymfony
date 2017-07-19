<?php

namespace Tests\AdminBundle\Controller;


use CoreBundle\Repository\UserRepository;
use CoreBundle\Service\User\UserService;
use Liip\FunctionalTestBundle\Test\WebTestCase;
use PHPUnit\Framework\Assert;
use Symfony\Component\HttpFoundation\Response;

class UserControllerTest extends WebTestCase
{
    /**
     * @var UserService
     */
    protected $userService;

    public function setUp()
    {
        parent::setUp();
        $this->userService = $this->getContainer()->get('startsymfony.core.user_service');
    }

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


        $client->request('GET', '/admin/users');
        $this->assertStatusCode(Response::HTTP_FORBIDDEN, $client);

        $client->request('GET', '/logout');


        $crawler = $client->request('GET', '/login');
        $form = $crawler->selectButton('Login')->form();
        $form->setValues(['_username' => 'admin_user@gmail.com', '_password' => 'password']);
        $client->submit($form);

        $client->request('GET', '/admin/users');
        $this->assertStatusCode(Response::HTTP_OK, $client);

    }

    /**
     * Tests that we can change the user email
     * We try to change the email to something that exists then we try to we actually change it
     * We confirm that the new email exists in our database
     */
    public function testChangeEmail()
    {
        $client = $this->makeClient();
        $crawler = $client->request('GET', '/login');
        $form = $crawler->selectButton('Login')->form();
        $form->setValues(['_username' => 'admin_user@gmail.com', '_password' => 'password']);
        $client->submit($form);

        $user = $this->userService->findUserByEmail('change_email_user@gmail.com');

        $client->request('PATCH', '/admin/users/' . $user->getId() . '/email', ['email' => 'admin_user@gmail.com']);
        $this->assertStatusCode(Response::HTTP_BAD_REQUEST, $client);

        $client->request('PATCH', '/admin/users/' . $user->getId() . '/email', ['email' => 'change_email_user_1@gmail.com']);
        $this->assertStatusCode(Response::HTTP_NO_CONTENT, $client);

        Assert::assertTrue($this->userService->doesEmailExist('change_email_user_1@gmail.com'));
    }

    /**
     * Tests that a user can be disabled
     */
    public function testUserDisablingUser()
    {
        $user = $this->userService->findUserByEmail('disable_user@gmail.com');
        Assert::assertTrue($user->isEnabled());

        $client = $this->makeClient();
        $crawler = $client->request('GET', '/login');
        $form = $crawler->selectButton('Login')->form();
        $form->setValues(['_username' => 'admin_user@gmail.com', '_password' => 'password']);
        $client->submit($form);

        $client->request('PATCH', '/admin/users/' . $user->getId() . '/enable/0');
        $this->assertStatusCode(Response::HTTP_NO_CONTENT, $client);

        $this->getContainer()->get('doctrine.orm.entity_manager')->refresh($user);
        Assert::assertFalse($user->isEnabled());
    }

    /**
     * Tests that an admin can change the password of another user
     */
    public function testUserChangePassword()
    {
        $user = $this->userService->findUserByEmail('change_password_user@gmail.com');
        $encoder = $this->getContainer()->get('security.encoder_factory')->getEncoder($user);

        Assert::assertTrue($encoder->isPasswordValid($user->getPassword(), 'password', $user->getSalt()));

        $client = $this->makeClient();
        $crawler = $client->request('GET', '/login');
        $form = $crawler->selectButton('Login')->form();
        $form->setValues(['_username' => 'admin_user@gmail.com', '_password' => 'password']);
        $client->submit($form);

        $client->request('PATCH', '/admin/users/' . $user->getId() . '/password', ['password' => 'new_password']);
        $this->assertStatusCode(Response::HTTP_NO_CONTENT, $client);

        $this->getContainer()->get('doctrine.orm.entity_manager')->refresh($user);
        Assert::assertTrue($encoder->isPasswordValid($user->getPassword(), 'new_password', $user->getSalt()));
    }

    /**
     * Testing that I can toggle role admin for a regular user
     */
    public function testToggleAdminFeature()
    {
        $user = $this->userService->findUserByEmail('future_admin_user@gmail.com');
        Assert::assertEquals(['ROLE_USER'], $user->getRoles());

        $client = $this->makeClient();
        $crawler = $client->request('GET', '/login');
        $form = $crawler->selectButton('Login')->form();
        $form->setValues(['_username' => 'admin_user@gmail.com', '_password' => 'password']);
        $client->submit($form);


        $client->request('PATCH', '/admin/users/' . $user->getId() . '/admin-toggle/1');
        $this->assertStatusCode(Response::HTTP_NO_CONTENT, $client);

        $this->getContainer()->get('doctrine.orm.entity_manager')->refresh($user);
        Assert::assertEquals(['ROLE_ADMIN'], $user->getRoles());
    }
}