<?php

namespace StarterKit\StartBundle\Tests\Controller;

use StarterKit\StartBundle\Controller\SecurityController;

class LoginControllerFakeTest extends BaseApiTestCase
{


    public function testLoginHittingController()
    {
        $this->expectException(\LogicException::class);
        $controller = new SecurityController();
        $controller->loginAction();
    }
}