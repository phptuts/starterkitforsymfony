<?php

namespace ApiBundle\Controller;


use FOS\RestBundle\Controller\Annotations\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\HttpFoundation\Response;

class UserController
{
    /**
     * @Security("has_role('ROLE_USER')")
     * @Route(name="api_test", path="/api/test")
     */
    public function testAction()
    {
        return new Response('worked');
    }
}