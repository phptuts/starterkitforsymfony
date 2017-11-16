<?php

namespace StarterKit\StartBundle\Security\Voter;

use StarterKit\StartBundle\Entity\BaseUser;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

/**
 * Class UserVoter
 * @package StarterKit\StartBundle\Security\Voter
 */
class UserVoter extends Voter
{
    /**
     * The attribute the voter votes on to see if the user can view and edit the User
     * @var string
     */
    const USER_CAN_VIEW_EDIT = 'user_can_view';

    /**
     * Determines if the voter should vote.  If the what we are voting on is a User and if the attribute is USER_CAN_VIEW_EDIT vote.
     *
     *
     * @param string $attribute An attribute
     * @param mixed $subject The subject to secure, e.g. an object the user wants to access or any other PHP type
     *
     * @return bool True if the attribute and subject are supported, false otherwise
     */
    protected function supports($attribute, $subject)
    {
         return $attribute == self::USER_CAN_VIEW_EDIT && $subject instanceof BaseUser;
    }

    /**
     * Allows the user to view / edit if the user is an admin or the user is trying to edit itself.
     *
     * @param string $attribute
     * @param BaseUser $subject
     * @param TokenInterface $token
     *
     * @return bool
     */
    protected function voteOnAttribute($attribute, $subject, TokenInterface $token)
    {
        /** @var BaseUser $user */
        $user = $token->getUser();

        return $user instanceof BaseUser && ($user->hasRole('ROLE_ADMIN') || $user->getId() === $subject->getId());
    }

}