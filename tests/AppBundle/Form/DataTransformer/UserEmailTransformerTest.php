<?php

namespace Tests\AppBundle\Form\DataTransformer;

use AppBundle\Entity\User;
use AppBundle\Form\DataTransformer\UserEmailTransformer;
use AppBundle\Repository\UserRepository;
use AppBundle\Service\User\UserService;
use Mockery\Mock;
use PHPUnit\Framework\Assert;
use Symfony\Component\Form\Exception\TransformationFailedException;
use Tests\BaseTestCase;

class UserEmailTransformerTest extends BaseTestCase
{

    /**
     * @var UserRepository|Mock
     */
    protected $userService;

    /**
     * @var UserEmailTransformer
     */
    protected $userEmailTransformer;

    public function setUp()
    {
        $this->userService = \Mockery::mock(UserService::class);
        $this->userEmailTransformer = new UserEmailTransformer($this->userService);
    }

    /**
     * If a user is not found transform exception should be thrown
     */
    public function testReserveTransformNoEmailFound()
    {
        $user = new User();
        $user->setEmail('example@gmail.com');

        $this->userService
            ->shouldReceive('findUserByEmail')
            ->once()
            ->with($user->getEmail())
            ->andReturnNull();

        $this->expectException(TransformationFailedException::class);
        $this->userEmailTransformer->reverseTransform($user);
    }

    /**
     * If a user is found it should pass the found user
     */
    public function testReserveTransformerEmailFound()
    {
        $user = new User();
        $user->setEmail('example@gmail.com');

        $userFound = new User();

        $this->userService
            ->shouldReceive('findUserByEmail')
            ->once()
            ->with($user->getEmail())
            ->andReturn($userFound);

        $userRet = $this->userEmailTransformer->reverseTransform($user);
        Assert::assertEquals($userFound, $userRet);
    }

    /**
     * Testing that an email with no user returns the empty user
     * The empty user won't have an email attached to it so it will trigger the NotBlank validation error
     */
    public function testNoEmailSetOnUserReturnsNull()
    {
        $userRet = $this->userEmailTransformer->reverseTransform(new User());

        // asserting the email is blank
        Assert::assertNull($userRet->getEmail());
    }

    /**
     * We want transform to always return a user so that it render the form correctly
     */
    public function testTransformOnEmptyUser()
    {
        $userRet = $this->userEmailTransformer->transform(null);

        Assert::assertInstanceOf(User::class, $userRet);
    }

    /**
     * If a user is on the form it should just pass through this method
     */
    public function testTransformOnUser()
    {
        $user = new User();
        $userRet = $this->userEmailTransformer->transform($user);

        Assert::assertEquals($user, $userRet);
    }
}