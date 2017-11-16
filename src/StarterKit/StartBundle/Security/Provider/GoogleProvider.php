<?php

namespace StarterKit\StartBundle\Security\Provider;

use StarterKit\StartBundle\Entity\BaseUser;
use StarterKit\StartBundle\Exception\ProgrammerException;
use StarterKit\StartBundle\Factory\GoogleClientFactory;
use StarterKit\StartBundle\Service\UserService;
use StarterKit\StartBundle\Service\UserServiceInterface;
use Symfony\Component\Security\Core\Exception\UsernameNotFoundException;
use Symfony\Component\Security\Core\User\UserProviderInterface;

/**
 * Class GoogleProvider
 * @package StarterKit\StartBundle\Security\Provider
 */
class GoogleProvider implements GoogleProviderInterface
{
    use CustomProviderTrait;

    /**
     * @var \Google_Client
     */
    private $googleClient;


    public function __construct(
        GoogleClientFactory $googleClientFactory,
        UserServiceInterface $userService)
    {
        $this->userService = $userService;
        $this->googleClient = $googleClientFactory->getClient();
    }

    /**
     * 1) We validate the access token by fetching the user information
     * 2) We search for google user id and if one is found we return that user
     * 3) Then we search by email and if one is found we update the user to have that google user id
     * 4) If nothing is found we register the user with google user id
     * @param string $username
     * @return BaseUser
     */
    public function loadUserByUsername($username)
    {
        try {
            $payload = $this->googleClient->verifyIdToken($username);
            $email = $payload['email'];
            $googleUserId = $payload['sub'];

            $user = $this->userService->findByGoogleUserId($googleUserId);

            if (!empty($user)) {
                return $user;
            }

            $user = $this->userService->findUserByEmail($email);

            // This means that the user is registering for the first time
            if (!empty($user)) {
                $this->updateUserWithGoogleUserId($user, $googleUserId);
                return $user;
            }

            return $this->registerUser($email, $googleUserId);
        }
        catch (\LogicException $ex) {
            throw new UsernameNotFoundException("Google AuthToken Did Not validate, ERROR MESSAGE " . $ex->getMessage(), ProgrammerException::GOOGLE_USER_PROVIDER_LOGIC_EXCEPTION);

        }
        catch (\Exception $ex) {
            throw new UsernameNotFoundException("Google AuthToken Did Not validate, ERROR MESSAGE " . $ex->getMessage(), ProgrammerException::GOOGLE_USER_PROVIDER_EXCEPTION);
        }
    }

    /**
     * Updates the user with their google id creating a link between their google account and user information.
     *
     * @param BaseUser $user
     * @param string $googleUserId
     */
    protected function updateUserWithGoogleUserId(BaseUser $user, $googleUserId)
    {
        $user->setGoogleUserId($googleUserId);
        $this->userService->save($user);
    }

    /**
     * We register the user with their google id and email.
     *
     * @param string $email
     * @param string $googleUserId
     * @return BaseUser
     */
    protected function registerUser($email, $googleUserId)
    {
        $className = $this->userService->getUserClass();
        /** @var BaseUser $user */
        $user = (new $className());
        $user->setEmail($email)
            ->setGoogleUserId($googleUserId)
            ->setPlainPassword(base64_encode(random_bytes(20)));
        $this->userService->registerUser($user, UserService::SOURCE_TYPE_GOOGLE);

        return $user;
    }

}