<?php

namespace CoreBundle\Service;

use CoreBundle\Entity\User;

/**
 * TODO TEST
 * Class EmailService
 * @package CoreBundle\Service
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
     * @param User $user
     */
    public function sendRegisterEmail(User $user)
    {
        $message = (new \Swift_Message('Thanks for registering.'))
            ->setFrom($this->fromEmail)
            ->setTo($user->getEmail())
            ->setBody(
                $this->twig->render('@App/email/register.html.twig', ['user' => $user]),
                'text/html'
            );

        $this->mailer->send($message);
    }

    /**
     * Send the forget password email
     *
     * @param User $user
     */
    public function sendForgetPasswordEmail(User $user)
    {
        $message = (new \Swift_Message('Reset Password'))
            ->setFrom($this->fromEmail)
            ->setTo($user->getEmail())
            ->setBody(
                $this->twig->render('@App/email/forget-password.html.twig', ['user' => $user]),
                'text/html'
            );

        $this->mailer->send($message);

    }
}