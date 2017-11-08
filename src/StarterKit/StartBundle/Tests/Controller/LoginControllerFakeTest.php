<?php

namespace StarterKit\StartBundle\Tests\Controller;

use Mockery\Mock;
use StarterKit\StartBundle\Controller\UserController;
use StarterKit\StartBundle\Service\AuthResponseService;
use StarterKit\StartBundle\Service\FormSerializer;
use StarterKit\StartBundle\Service\S3Service;
use StarterKit\StartBundle\Service\UserService;


class LoginControllerFakeTest extends BaseApiTestCase
{
    /**
     * @var S3Service|Mock
     */
    protected $s3Service;

    /**
     * @var UserService|Mock
     */
    protected $userService;

    /**
     * @var FormSerializer|Mock
     */
    protected $formSerializer;

    /**
     * @var AuthResponseService|Mock
     */
    protected $authResponseService;

    /**
     * @var UserController
     */
    protected $userController;

    public function setUp()
    {
        parent::setUp();

        $this->s3Service = \Mockery::mock(S3Service::class);
        $this->userService = \Mockery::mock(UserService::class);
        $this->authResponseService = \Mockery::mock(AuthResponseService::class);
        $this->formSerializer = \Mockery::mock(FormSerializer::class);

        $this->userController = new UserController(
            $this->getContainer()->get('StarterKit\StartBundle\Service\FormSerializer'),
            $this->userService,
            $this->authResponseService,
            $this->s3Service
        );

        $this->userController->setContainer($this->getContainer());
    }

    public function testLoginController()
    {
        $this->expectException(\LogicException::class);
        $this->userController->loginAction();
    }
}