<?php

namespace AppBundle\Form\DataTransformer;

use AppBundle\Entity\User;
use AppBundle\Repository\UserRepository;
use AppBundle\Service\User\UserService;
use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\Exception\TransformationFailedException;

/**
 * This transformer is used to transform the whole form.
 *
 * Class UserEmailTransformer
 * @package AppBundle\Form\DataTransformer
 */
class UserEmailTransformer implements DataTransformerInterface
{
    /**
     * @var UserService
     */
    private $userService;

    public function __construct(UserService $userService)
    {
        $this->userService = $userService;
    }

    /**
     * This always return null or the full object because we transforming the whole form with this transformer
     *
     * @param User $user
     * @return string
     */
    public function transform($user)
    {
        if (empty($user)) {
            return new User();
        }

        return $user;

    }

    /**
     * This will always receive a full user object because we are transforming the whole form
     * If the we can't find the user we throw a transformation exception
     * @param User $user
     *
     * @return User
     */
    public function reverseTransform($user)
    {
        // If the email field is we pass the emtpy user back
        if (empty($user->getEmail())) {
            return $user;
        }

        $user = $this->userService->findUserByEmail($user->getEmail());

        if (null === $user) {
            throw new TransformationFailedException('Email was not found.');
        }

        return $user;
    }

}