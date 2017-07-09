<?php

namespace Tests\AdminBundle\Controller;


use CoreBundle\Repository\UserRepository;
use Liip\FunctionalTestBundle\Test\WebTestCase;
use PHPUnit\Framework\Assert;
use Symfony\Component\HttpFoundation\Response;

class UserControllerTest extends WebTestCase
{
    /**
     * @var UserRepository
     */
    protected $userRepository;

    public function setUp()
    {
        parent::setUp();
        $this->userRepository = $this->getContainer()->get('startsymfony.core.repository.user_repository');
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

        $user = $this->userRepository->findUserByEmail('change_email_user@gmail.com');

        $client->request('PATCH', '/admin/users/' . $user->getId() . '/email', ['email' => 'change_email_user@gmail.com']);
        $this->assertStatusCode(Response::HTTP_BAD_REQUEST, $client);

        $client->request('PATCH', '/admin/users/' . $user->getId() . '/email', ['email' => 'change_email_user_1@gmail.com']);
        $this->assertStatusCode(Response::HTTP_NO_CONTENT, $client);

        Assert::assertTrue($this->userRepository->doesEmailExist('change_email_user_1@gmail.com'));
    }

    /**
     * Tests that a user can be disabled
     */
    public function testUserDisablingUser()
    {
        $user = $this->userRepository->findUserByEmail('disable_user@gmail.com');
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

    public function testUserChangePassword()
    {
        $user = $this->userRepository->findUserByEmail('change_password_user@gmail.com');
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
}