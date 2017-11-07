<?php

namespace AppBundle\Service;

use StarterKit\StartBundle\Entity\BaseUser;

/**
 * Class EmailService
 * @package AppBundle\Service
 */
class EmailService
{

    /**
     * @var \Twig_Environment
     */
    protected $twig;

    /**
     * @var \Swift_Mailer
     */
    protected $mailer;

    /**
     * @var string
     */
    protected $fromEmail;

    /**
     * EmailService constructor.
     * @param \Twig_Environment $twig
     * @param \Swift_Mailer $mailer
     * @param $fromEmail
     */
    public function __construct(\Twig_Environment $twig, \Swift_Mailer $mailer, $fromEmail)
    {
        $this->twig = $twig;
        $this->mailer = $mailer;
        $this->fromEmail = $fromEmail;
    }

    /**
     * Sends the registration email
     *
     * @param BaseUser $user
     */
    public function sendRegisterEmail(BaseUser $user)
    {
        $message = (new \Swift_Message('Thanks for registering.'))
            ->setFrom($this->fromEmail)
            ->setTo($user->getEmail())
            ->setBody(
                $this->twig->render('@App/main/email/register.html.twig', ['user' => $user, 'title' => 'Thank you for registering']),
                'text/html'
            );

        $this->mailer->send($message);
    }

    /**
     * Send the forget password email
     *
     * @param BaseUser $user
     */
    public function sendForgetPasswordEmail(BaseUser $user)
    {
        $message = (new \Swift_Message('Reset Password'))
            ->setFrom($this->fromEmail)
            ->setTo($user->getEmail())
            ->setBody(
                $this->twig->render('@App/main/email/forget-password.html.twig', ['user' => $user, 'title' => 'Forget Password']),
                'text/html'
            );

        $this->mailer->send($message);

    }
}