<?php

namespace Tests\CoreBundle\Service\User;

use CoreBundle\Entity\User;
use CoreBundle\Exception\ProgrammerException;
use CoreBundle\Service\User\UserService;
use Doctrine\ORM\EntityManager;
use Mockery\Mock;
use PHPUnit\Framework\Assert;
use Symfony\Component\Security\Core\Encoder\EncoderFactory;
use Symfony\Component\Security\Core\Encoder\PasswordEncoderInterface;
use Symfony\Component\Serializer\Encoder\EncoderInterface;
use Tests\BaseTestCase;

class UserServiceTest extends BaseTestCase
{
    /**
     * @var EntityManager|Mock
     */
    protected $em;

    /**
     * @var EncoderFactory|Mock
     */
    protected $encoderFactory;

    /**
     * @var UserService
     */
    protected $userService;

    public function setUp()
    {
        $this->encoderFactory = \Mockery::mock(EncoderFactory::class);
        $this->em = \Mockery::mock(EntityManager::class);
        $this->userService = new UserService($this->em, $this->encoderFactory);
    }

    public function testServiceDefinition()
    {
        Assert::assertInstanceOf(UserService::class, $this->getContainer()->get('startsymfony.core.user_service'));
    }

    public function testSavePlainPasswordNotSet()
    {
        $user = new User();
        $user->setPlainPassword(null);
        $this->expectException(ProgrammerException::class);
        $this->expectExceptionCode(ProgrammerException::NO_PLAIN_PASSWORD_ON_USER_ENTITY_EXCEPTION_CODE);

        $this->userService->saveUserWithPlainPassword($user);
    }

    public function testSavePlainPassword()
    {
        $user = new User();
        $user->setPlainPassword('password');

        $encoder = \Mockery::mock(PasswordEncoderInterface::class);
        $encoder->shouldReceive('encodePassword')->with('password', null)->andReturn('adfasdf3dafsa');
        $this->encoderFactory->shouldReceive('getEncoder')->with($user)->andReturn($encoder);

        $this->em->shouldReceive('persist')->once()->with($user);
        $this->em->shouldReceive('flush')->once();

        $this->userService->saveUserWithPlainPassword($user);

        Assert::assertEquals('adfasdf3dafsa', $user->getPassword());
    }

    public function testResetPassword()
    {
        $user = new User();
        $user->setPlainPassword('password')->setForgetPasswordToken('token')->setForgetPasswordExpired(new \DateTime());

        $encoder = \Mockery::mock(PasswordEncoderInterface::class);
        $encoder->shouldReceive('encodePassword')->with('password', null)->andReturn('adfasdf3dafsa');
        $this->encoderFactory->shouldReceive('getEncoder')->with($user)->andReturn($encoder);

        $this->em->shouldReceive('persist')->once()->with($user);
        $this->em->shouldReceive('flush')->once();

        $this->userService->saveUserForResetPassword($user);

        Assert::assertEquals('adfasdf3dafsa', $user->getPassword());
        Assert::assertEmpty($user->getForgetPasswordToken());
        Assert::assertEmpty($user->getForgetPasswordExpired());
    }
}