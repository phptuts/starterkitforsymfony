<?php

namespace AppBundle\Service\User;


use AppBundle\Entity\User;
use AppBundle\Exception\ProgrammerException;
use AppBundle\Repository\UserRepository;
use AppBundle\Service\AbstractEntityService;
use Doctrine\ORM\EntityManager;
use Symfony\Component\Security\Core\Encoder\EncoderFactory;

/**
 * Class UserService
 * @package AppBundle\Service\User
 */
class UserService extends AbstractEntityService
{
    /**
     * @var EncoderFactory
     */
    protected $encoderFactory;
    /**
     * @var UserRepository
     */
    private $userRepository;


    public function __construct(
        EntityManager $em,
        EncoderFactory $encoderFactory,
        UserRepository $userRepository
    ) {
        parent::__construct($em);
        $this->encoderFactory = $encoderFactory;
        $this->userRepository = $userRepository;
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

    /**
     * Finds a user by email
     *
     * @param $email
     * @return User|null
     */
    public function findUserByEmail($email)
    {
        return $this->userRepository->findUserByEmail($email);
    }

    /**
     * Finds a user by their id
     *
     * @param $id
     * @return User|null|object
     */
    public function findUserById($id)
    {
        return $this->userRepository->find($id);
    }

    /**
     * Finds a user by their facebook user id
     *
     * @param $facebookUserId
     * @return User|null|object
     */
    public function findByFacebookUserId($facebookUserId)
    {
        return $this->userRepository->findByFacebookUserId($facebookUserId);
    }

    /**
     * Finds a user by their google user id
     *
     * @param $googleUserId
     * @return User|null|object
     */
    public function findByGoogleUserId($googleUserId)
    {
        return $this->userRepository->findByGoogleUserId($googleUserId);
    }

    /**
     * Return a paginator of users
     *
     * @param $searchTerm
     * @param int $page
     * @return \Doctrine\ORM\Tools\Pagination\Paginator
     */
    public function searchUser($searchTerm, $page = 1)
    {
        return $this->userRepository->getUsers($searchTerm, $page);
    }

    /**
     * Returns true if the email exists
     *
     * @param string $email
     * @return bool
     */
    public function doesEmailExist($email)
    {
        return !empty($this->findUserByEmail($email));
    }

    /**
     * Finds the user by the forgot password token
     *
     * @param string $token
     * @return User|null
     */
    public function findUserByForgetPasswordToken($token)
    {
        return $this->userRepository->findUserByForgetPasswordToken($token);
    }

}