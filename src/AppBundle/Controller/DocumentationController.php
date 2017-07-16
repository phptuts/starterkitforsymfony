<?php

namespace AppBundle\Controller;


use FOS\RestBundle\Controller\FOSRestController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Response;

class DocumentationController extends FOSRestController
{
    /**
     * @Route("/docs", name="documentation")
     *
     * @return Response
     */
    public function documentationAction()
    {
        return $this->render('@App/documentation/documentation.html.twig');
    }

    /**
     * @Route("/docs/setup", name="doc_setup")
     *
     * @return Response
     */
    public function setupAction()
    {
        return $this->render('@App/documentation/setup.html.twig');
    }

    /**
     * @Route("/docs/security-yml", name="doc_security")
     *
     * @return Response
     */
    public function securityYmlAction()
    {
        return $this->render('@App/documentation/security-yml.html.twig');
    }

    /**
     * @Route("/docs/website-social-auth", name="doc_social_auth_website")
     *
     * @return Response
     */
    public function socialWebsiteAuthAction()
    {
        return $this->render('@App/documentation/desktop-social-auth.html.twig');
    }

    /**
     * @Route("/docs/social-auth-high-level", name="doc_social_auth_high_level")
     *
     * @return Response
     */
    public function socialAuthHighLevelAction()
    {
        return $this->render('@App/documentation/social-auth-highlevel.html.twig');
    }

    /**
     * @Route("/docs/website-login-registration", name="doc_login_registration_website")
     *
     * @return Response
     */
    public function websiteLoginRegistrationAction()
    {
        return $this->render('@App/documentation/website-registration-login.html.twig');
    }

    /**
     * @Route("/docs/token-guard-workflow", name="doc_token_guard_workflow")
     *
     * @return Response
     */
    public function tokenGuardWorkflowAction()
    {
        return $this->render('@App/documentation/token-guard-workflow.html.twig');
    }

}