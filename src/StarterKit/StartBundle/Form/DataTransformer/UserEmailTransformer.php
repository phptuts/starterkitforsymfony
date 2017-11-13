<?php

namespace StarterKit\StartBundle\Form\DataTransformer;

use Doctrine\ORM\Query\Expr\Base;
use StarterKit\StartBundle\Entity\BaseUser;
use StarterKit\StartBundle\Service\UserServiceInterface;
use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\Exception\TransformationFailedException;

/**
 * This transformer is used to transform the whole form.
 *
 * Class UserEmailTransformer
 * @package StarterKit\StartBundle\Form\DataTransformer
 */
class UserEmailTransformer implements DataTransformerInterface
{
    /**
     * @var UserServiceInterface
     */
    private $userService;

    public function __construct(UserServiceInterface $userService)
    {
        $this->userService = $userService;
    }

    /**
     * This always return null or the full object because we transforming the whole form with this transformer
     *
     * @param BaseUser $user
     * @return string
     */
    public function transform($user)
    {
        if (empty($user)) {
            $className = $this->userService->getUserClass();

            return new $className();
        }

        return $user;

    }

    /**
     * This will always receive a full user object because we are transforming the whole form
     * If the we can't find the user we throw a transformation exception
     * @param BaseUser $user
     *
     * @return Base
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