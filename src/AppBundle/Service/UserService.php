<?php

namespace AppBundle\Service;

use AppBundle\Entity\User;
use AppBundle\Event\UserEvent;
use AppBundle\Exception\ProgrammerException;
use AppBundle\Model\Page\PageModel;
use AppBundle\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\Security\Core\Encoder\EncoderFactoryInterface;

/**
 * Class UserService
 * @package AppBundle\Service\User
 */
class UserService
{
    /**
     * This means the user came registered the website
     * @var string
     */
    const SOURCE_TYPE_WEBSITE = 'website';

    /**
     * This means the user registered from the api
     * @var string
     */
    const SOURCE_TYPE_API = 'api';

    /**
     * This means the user registered from the google
     * @var string
     */
    const SOURCE_TYPE_GOOGLE = 'google';

    /**
     * This means the user registered from the facebook
     * @var string
     */
    const SOURCE_TYPE_FACEBOOK = 'facebook';

    /**
     * This is the event dispatched for registering a new user
     *
     * @var string
     */
    const REGISTER_EVENT = 'register_event';

    /**
     * This is the event dispatched when a new password token is generated
     *
     * @var string
     */
    const FORGET_PASSWORD_EVENT = 'forget_password_event';

    /**
     * @var EncoderFactoryInterface
     */
    protected $encoderFactory;

    /**
     * @var UserRepository
     */
    private $userRepository;

    /**
     * @var integer the number of seconds for a refresh token to live
     */
    private $refreshTokenTTL;

    /**
     * @var EntityManagerInterface
     */
    private $em;

    /**
     * @var EventDispatcherInterface
     */
    private $dispatcher;


    public function __construct(
        EntityManagerInterface $em,
        EncoderFactoryInterface $encoderFactory,
        EventDispatcherInterface $dispatcher,
        $refreshTokenTTL
    ) {
        $this->encoderFactory = $encoderFactory;
        $this->refreshTokenTTL = $refreshTokenTTL;
        $this->em = $em;
        $this->userRepository = $this->em->getRepository(User::class);
        $this->dispatcher = $dispatcher;
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
     * @param int $limit
     * @return PageModel
     */
    public function searchUser($searchTerm, $page = 1, $limit = 10)
    {
        $total = $this->userRepository->countNumberOfUserInSearch($searchTerm);

        $users = $this->userRepository->searchUsers($searchTerm, $page, $limit);

        $results = [];

        foreach ($users as $user) {
            $results[] = $user->listView();
        }

        return new PageModel($results, $page, $total, $limit);
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

    /**
     * Return a user with a matching refresh token
     *
     * @param $token
     * @return User|null
     */
    public function findUserByValidRefreshToken($token)
    {
        return $this->userRepository->findUserByValidRefreshToken($token);
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
     * Saves a new user to our database
     *
     * @param User $user
     * @param string $source
     *
     * @return User
     */
    public function registerUser(User $user, $source = self::SOURCE_TYPE_WEBSITE)
    {
        $user->setRoles(["ROLE_USER"])
            ->setSource($source)
            ->setEnabled(true);

        $this->saveUserWithPlainPassword($user);

        $this->dispatcher->dispatch(self::REGISTER_EVENT, new UserEvent($user));

        return $user;
    }

    /**
     * Creates a forget password token and sends a forget password email to the user
     * @param User $user
     *
     * @return User
     */
    public function forgetPassword(User $user)
    {
        $tokenExpires = (new \DateTime())->modify('+2 days');

        $user->setForgetPasswordToken(md5(uniqid(rand(), true)))
            ->setForgetPasswordExpired($tokenExpires);

        $this->save($user);

        $this->dispatcher->dispatch(self::FORGET_PASSWORD_EVENT, new UserEvent($user));

        return $user;
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
     * Saves the user with an updated refresh token
     *
     * @param User $user
     *
     * @return User
     */
    public function updateUserRefreshToken(User $user)
    {
        if (!$user->isRefreshTokenValid()) {
            $user->setRefreshToken(bin2hex(random_bytes(90)));
        }

        $expirationDate = new \DateTime();
        $expirationDate->modify('+' . $this->refreshTokenTTL . ' seconds');
        $user->setRefreshTokenExpire($expirationDate);
        $this->save($user);

        return $user;
    }

    /**
     * Saves an entity
     *
     * @param $entity
     */
    public function save($entity)
    {
        $this->em->persist($entity);
        $this->em->flush();
    }
}