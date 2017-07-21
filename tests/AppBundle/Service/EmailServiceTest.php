<?php

namespace Tests\AppBundle\Service;

use AppBundle\Entity\User;
use AppBundle\Service\EmailService;
use Mockery\Mock;
use Tests\BaseTestCase;

class EmailServiceTest extends BaseTestCase
{
    /**
     * @var \Twig_Environment
     */
    protected $twig;

    /**
     * @var \Swift_Mailer|Mock
     */
    protected $mailer;

    /**
     * @var EmailService
     */
    protected $emailService;

    public function setUp()
    {
        $this->twig = $this->getContainer()->get('twig');
        $this->mailer = \Mockery::mock(\Swift_Mailer::class);
        $this->emailService = new EmailService($this->twig, $this->mailer, 'email@gmail.com');
    }

    /**
     * Tests that we can send out a forgot password email.  Tests the twig compiles
     */
    public function testForgetPasswordEmail()
    {
        $user = new User();
        $user->setForgetPasswordToken('token')
                ->setForgetPasswordExpired(new \DateTime());

        $this->mailer->shouldReceive('send')->with(\Mockery::type(\Swift_Message::class))->once();
        $this->emailService->sendForgetPasswordEmail($user);
    }

    /**
     * Tests we can send out a register email and that the twig compiles
     */
    public function testRegisterEmail()
    {
        $user = new User();
        $user->setEmail('blah@gmail.com');

        $this->mailer->shouldReceive('send')->with(\Mockery::type(\Swift_Message::class))->once();
        $this->emailService->sendRegisterEmail($user);
    }
}