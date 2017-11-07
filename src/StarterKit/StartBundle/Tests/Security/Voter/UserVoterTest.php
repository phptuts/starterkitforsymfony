<?php

namespace StarterKit\StartBundle\Tests\Security\Voter;

use PHPUnit\Framework\Assert;
use StarterKit\StartBundle\Security\Voter\UserVoter;
use StarterKit\StartBundle\Tests\BaseTestCase;
use StarterKit\StartBundle\Tests\Entity\User;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\VoterInterface;

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

        $loggedInUser = new User();

        $this->setObjectId($loggedInUser, 15);
        $this->setObjectId($subject, 33);

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
        $this->setObjectId($loggedInUser, 15);
        $this->setObjectId($subject, 15);

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

        $loggedInUser = new User();
        $loggedInUser
            ->setRoles(['ROLE_ADMIN']);

        $this->setObjectId($loggedInUser, 15);
        $this->setObjectId($subject, 33);

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