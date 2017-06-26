<?php

namespace CoreBundle\Service\User;


use CoreBundle\Entity\User;
use CoreBundle\Exception\ProgrammerException;
use CoreBundle\Repository\UserRepository;
use CoreBundle\Service\AbstractEntityService;
use Doctrine\ORM\EntityManager;
use Symfony\Component\Security\Core\Encoder\EncoderFactory;

/**
 * Class UserService
 * @package CoreBundle\Service\User
 */
class UserService extends AbstractEntityService
{
    /**
     * @var EncoderFactory
     */
    protected $encoderFactory;


    public function __construct(EntityManager $em, EncoderFactory $encoderFactory)
    {
        parent::__construct($em);
        $this->encoderFactory = $encoderFactory;
    }

    /**
     * @param User $user
     * @throws ProgrammerException
     */
    public function saveUserWithPlainPassword(User $user)
    {
        if (empty($user->getPlainPassword())) {
            throw new ProgrammerException("Plain Password must be set.", ProgrammerException::NO_PLAIN_PASSWORD_ON_USER_ENTITY_EXCEPTION_CODE);
        }

        $encoder = $this->encoderFactory->getEncoder($user);
        $user->setPassword($encoder->encodePassword($user->getPlainPassword(), $user->getSalt()));
        $this->save($user);
    }

    /**
     * Makes sure the forget password token and forget password token expiration time are set to null
     *
     * @param User $user
     */
    public function saveUserForResetPassword(User $user)
    {
        $user->setForgetPasswordToken(null)
            ->setForgetPasswordExpired(null);

        $this->saveUserWithPlainPassword($user);
    }

}