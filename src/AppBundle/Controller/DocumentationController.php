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

    /**
     * @Route("/docs/api-login-guard", name="doc_api_login_guard")
     *
     * @return Response
     */
    public function tokenApiLoginTokenGuard()
    {
        return $this->render('@App/documentation/api-login-guard.html.twig');
    }

    /**
     * @Route("/docs/refresh-tokens", name="doc_refresh_tokens")
     *
     * @return Response
     */
    public function refreshTokenAction()
    {
        return $this->render('@App/documentation/refresh-tokens.html.twig');
    }

    /**
     * @Route("/docs/token-provider", name="doc_token_provider")
     *
     * @return Response
     */
    public function tokenProviderAction()
    {
        return $this->render('@App/documentation/token-provider.html.twig');
    }

    /**
     * @Route("/docs/jws_jwt_token", name="doc_jwt_token")
     *
     * @return Response
     */
    public function jwsJwtTokenAction()
    {
        return $this->render('@App/documentation/jwt-jws-token.html.twig');
    }

    /**
     * @Route("/docs/credential_response", name="doc_credential_response")
     *
     * @return Response
     */
    public function credentialResponseAction()
    {
        return $this->render('@App/documentation/credentials.html.twig');
    }

    /**
     * @Route("/docs/user_voter", name="doc_user_voter")
     *
     * @return Response
     */
    public function userVoterAction()
    {
        return $this->render('@App/documentation/user-voter.html.twig');
    }

    /**
     * @Route("/docs/custom_file_upload_twig", name="doc_custom_file_upload_twig")
     *
     * @return Response
     */
    public function customFileUploadTwigAction()
    {
        return $this->render('@App/documentation/custom-file-upload.html.twig');
    }

    /**
     * @Route("/docs/s3_file_uploads", name="doc_s3_file_uploads")
     *
     * @return Response
     */
    public function s3FileUploadsAction()
    {
        return $this->render('@App/documentation/s3-file-upload.html.twig');
    }

    /**
     * @Route("/docs/emails", name="doc_emails")
     *
     * @return Response
     */
    public function emailsAction()
    {
        return $this->render('@App/documentation/email.html.twig');
    }

    /**
     * @Route("/docs/forget-password-workflow", name="doc_forget_password_workflow")
     *
     * @return Response
     */
    public function forgetPasswordWorkflowAction()
    {
        return $this->render('@App/documentation/forget-password-workflow.html.twig');
    }

    /**
     * @Route("/docs/account-setting-mobile-nav", name="doc_account_setting_mobile_nav")
     *
     * @return Response
     */
    public function accountSettingMobileNavAction()
    {
        return $this->render('@App/documentation/account-setting-mobile-nav.html.twig');
    }

    /**
     * @Route("/docs/change-password", name="doc_change_password")
     *
     * @return Response
     */
    public function changePasswordAction()
    {
        return $this->render('@App/documentation/change-password.html.twig');
    }

    /**
     * @Route("/docs/update_user_profile", name="doc_update_profile")
     *
     * @return Response
     */
    public function updateUserProfileAction()
    {
        return $this->render('@App/documentation/update-user-profile.html.twig');
    }

    /**
     * @Route("/docs/admin_user_management", name="doc_admin_user_management")
     *
     * @return Response
     */
    public function adminUserManagementAction()
    {
        return $this->render('@App/documentation/admin-user-management.html.twig');
    }

    /**
     * @Route("/docs/fos_form_handler", name="doc_fos_form_handler")
     *
     * @return Response
     */
    public function fosFormErrorHandler()
    {
        return $this->render('@App/documentation/fos-form-handler.html.twig');
    }

    /**
     * @Route("/docs/exception_handling", name="doc_exception_handling")
     *
     * @return Response
     */
    public function exceptionHandlingAction()
    {
        return $this->render('@App/documentation/exception-handling.html.twig');
    }

    /**
     * @Route("/docs/api_bundles", name="doc_api_bundles")
     *
     * @return Response
     */
    public function apiBundlesAction()
    {
        return $this->render('@App/documentation/api-bundles.html.twig');
    }

}