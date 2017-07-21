<?php

namespace Tests\AppBundle\Security\Voter;

use AppBundle\Entity\User;
use AppBundle\Security\Voter\UserVoter;
use PHPUnit\Framework\Assert;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\VoterInterface;
use Tests\BaseTestCase;

class UserVoterTest extends BaseTestCase
{
    /**
     * @var UserVoter
     */
    protected $userVoter;

    public function setUp()
    {
        parent::setUp();
        $this->userVoter = new UserVoter();
    }

    /**
     * Tests that a non logged in user can't edit or view logged in users
     */
    public function testNonLoggedInUserIsDenied()
    {
        $user = new User();
        $token = \Mockery::mock(TokenInterface::class);
        $token->shouldReceive('getUser')->andReturn('anon.');


        Assert::assertEquals(VoterInterface::ACCESS_DENIED, $this->userVoter->vote($token, $user, [UserVoter::USER_CAN_VIEW_EDIT]));
    }

    /**
     * Tests that a non admin user can't view / edit othe users
     */
    public function testLoginUserTryingToAccessAnotherUserIsDenied()
    {
        $subject = new User();
        $subject->setEmail('bill@gmail.com');

        $loggedInUser = new User();
        $loggedInUser->setEmail('blue@gmai.com');

        $token = \Mockery::mock(TokenInterface::class);
        $token->shouldReceive('getUser')->andReturn($loggedInUser);


        Assert::assertEquals(VoterInterface::ACCESS_DENIED, $this->userVoter->vote($token, $subject, [UserVoter::USER_CAN_VIEW_EDIT]));

    }

    /**
     * Tests that a logged in user can edit / view itself
     */
    public function testLoginUserCanViewEditItself()
    {
        $subject = new User();
        $subject->setEmail('bill@gmail.com');

        $loggedInUser = new User();
        $loggedInUser->setEmail('bill@gmail.com');

        $token = \Mockery::mock(TokenInterface::class);
        $token->shouldReceive('getUser')->andReturn($loggedInUser);


        Assert::assertEquals(VoterInterface::ACCESS_GRANTED, $this->userVoter->vote($token, $subject, [UserVoter::USER_CAN_VIEW_EDIT]));
    }

    /**
     * Tests that an admin user can view / edit another user
     */
    public function testAdminUserCanEditViewAnotherUser()
    {
        $subject = new User();
        $subject->setEmail('bill@gmail.com');

        $loggedInUser = new User();
        $loggedInUser->setEmail('bill@gmail.com')
            ->setRoles(['ROLE_ADMIN']);

        $token = \Mockery::mock(TokenInterface::class);
        $token->shouldReceive('getUser')->andReturn($loggedInUser);


        Assert::assertEquals(VoterInterface::ACCESS_GRANTED, $this->userVoter->vote($token, $subject, [UserVoter::USER_CAN_VIEW_EDIT]));

    }

    /**
     * Tests that the voter only votes on user edit / views
     */
    public function testVoterDoesNotVoteOnOtherThings()
    {
        $token = \Mockery::mock(TokenInterface::class);
        $token->shouldReceive('getUser')->andReturn('anon.');

        Assert::assertEquals(VoterInterface::ACCESS_ABSTAIN, $this->userVoter->vote($token, [], ['people_test']));
        Assert::assertEquals(VoterInterface::ACCESS_ABSTAIN, $this->userVoter->vote($token, [], ['another']));
        Assert::assertEquals(VoterInterface::ACCESS_ABSTAIN, $this->userVoter->vote($token, [], ['yes']));

    }
}