<?php

namespace AppBundle\Security\Provider;

use AppBundle\Entity\User;
use AppBundle\Exception\ProgrammerException;
use AppBundle\Factory\GoogleClientFactory;
use AppBundle\Service\UserService;
use Symfony\Component\Security\Core\Exception\UsernameNotFoundException;
use Symfony\Component\Security\Core\User\UserProviderInterface;

/**
 * Class GoogleProvider
 * @package AppBundle\Security\Provider
 */
class GoogleProvider implements UserProviderInterface
{
    use CustomProviderTrait;

    /**
     * @var \Google_Client
     */
    private $googleClient;


    public function __construct(
        GoogleClientFactory $googleClientFactory,
        UserService $userService
    )
    {
        $this->userService = $userService;
        $this->googleClient = $googleClientFactory->getGoogleClient();
    }

    /**
     * 1) We validate the access token by fetching the user information
     * 2) We search for google user id and if one is found we return that user
     * 3) Then we search by email and if one is found we update the user to have that google user id
     * 4) If nothing is found we register the user with google user id
     * @param string $username
     * @return User
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
     * @param User $user
     * @param string $googleUserId
     */
    protected function updateUserWithGoogleUserId(User $user, $googleUserId)
    {
        $user->setGoogleUserId($googleUserId);
        $this->userService->save($user);
    }

    /**
     * We register the user with their google id and email.
     *
     * @param string $email
     * @param string $googleUserId
     * @return User
     */
    protected function registerUser($email, $googleUserId)
    {
        $user = (new User())
            ->setEmail($email)
            ->setGoogleUserId($googleUserId)
            ->setPlainPassword(base64_encode(random_bytes(20)));
        $this->userService->registerUser($user, UserService::SOURCE_TYPE_GOOGLE);

        return $user;
    }

}