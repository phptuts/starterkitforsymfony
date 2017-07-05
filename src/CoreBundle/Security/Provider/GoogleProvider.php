<?php


namespace CoreBundle\Security\Provider;


use CoreBundle\Entity\User;
use CoreBundle\Exception\ProgrammerException;
use CoreBundle\Factory\GoogleClientFactory;
use CoreBundle\Repository\UserRepository;
use CoreBundle\Service\User\RegisterService;
use Symfony\Component\Security\Core\Exception\UsernameNotFoundException;

class GoogleProvider extends AbstractCustomProvider
{
    /**
     * @var RegisterService
     */
    private $registerService;

    /**
     * @var \Google_Client
     */
    private $googleClient;

    public function __construct(UserRepository $userRepository, RegisterService $registerService, GoogleClientFactory $googleClientFactory)
    {
        parent::__construct($userRepository);
        $this->registerService = $registerService;
        $this->googleClient = $googleClientFactory->getGoogleClient();
    }

    /**
     * @param string $username
     * @return User
     */
    public function loadUserByUsername($username)
    {
        try {
            $payload = $this->googleClient->verifyIdToken($username);
            $email = $payload['email'];
            $user = $this->userRepository->findUserByEmail($email);

            // This means that the user is registering for the first time
            if (empty($user)) {
                $user = (new User())->setEmail($email);
                $user->setPlainPassword(base64_encode(random_bytes(20)));
                $this->registerService->registerUser($user);
            }

            return $user;
        }
        catch (\LogicException $ex) {
            throw new UsernameNotFoundException("Google AuthToken Did Not validate, ERROR MESSAGE " . $ex->getMessage(), ProgrammerException::GOOGLE_USER_PROVIDER_LOGIC_EXCEPTION);

        }
        catch (\Exception $ex) {
            throw new UsernameNotFoundException("Google AuthToken Did Not validate, ERROR MESSAGE " . $ex->getMessage(), ProgrammerException::GOOGLE_USER_PROVIDER_EXCEPTION);
        }
    }

}