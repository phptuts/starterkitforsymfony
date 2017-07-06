<?php


namespace CoreBundle\Security\Provider;

use CoreBundle\Entity\User;
use CoreBundle\Exception\ProgrammerException;
use CoreBundle\Factory\FaceBookClientFactory;
use CoreBundle\Repository\UserRepository;
use CoreBundle\Service\User\RegisterService;
use Doctrine\ORM\EntityManager;
use Facebook\Exceptions\FacebookResponseException;
use Facebook\Exceptions\FacebookSDKException;
use Facebook\FacebookClient;
use Symfony\Component\Security\Core\Exception\UsernameNotFoundException;
use Symfony\Component\Security\Core\User\UserInterface;

class FacebookProvider extends AbstractCustomProvider
{

    /**
     * @var FacebookClient
     */
    protected $facebookClient;


    /**
     * @var EntityManager
     */
    private $registerService;

    public function __construct(
        FaceBookClientFactory $faceBookClientFactory,
        UserRepository $userRepository,
        RegisterService $registerService
    )
    {
        parent::__construct($userRepository);
        $this->facebookClient = $faceBookClientFactory->getFacebookClient();
        $this->registerService = $registerService;
    }

    /**
     * Loads the user for the given email.
     *
     * This method must throw UsernameNotFoundException if the user is not
     * found.
     *
     * @param string $username The facebook auth token used to fetch the user
     *
     * @return UserInterface
     *
     * @throws UsernameNotFoundException if the user is not found
     */
    public function loadUserByUsername($username)
    {
        try {
            // If you want to request the user's picture you can picture
            // You can also specify the picture height using picture.height(500).width(500)
            // Be sure request the scope param in the js
            $response = $this->facebookClient->get('/me?fields=email', $username);
            $email = $response->getGraphUser()->getEmail();
            $user = $this->userRepository->findUserByEmail($email);

            // This means that the user is registering for the first time
            if (empty($user)) {
                $user = (new User())->setEmail($email);
                $user->setPlainPassword(base64_encode(random_bytes(20)));
                $this->registerService->registerUser($user);
            }

            return $user;
        } catch (FacebookResponseException $ex) {
            throw new UsernameNotFoundException("Facebook AuthToken Did Not validate, ERROR MESSAGE " . $ex->getMessage(), ProgrammerException::FACEBOOK_RESPONSE_EXCEPTION_CODE);
        } catch (FacebookSDKException $ex) {
            throw new UsernameNotFoundException("Facebook SDK failed, ERROR MESSAGE " . $ex->getMessage(), ProgrammerException::FACEBOOK_SDK_EXCEPTION_CODE);
        } catch (\Exception $ex) {
            throw new UsernameNotFoundException("Something unknown went wrong, ERROR MESSAGE  " . $ex->getMessage(), ProgrammerException::FACEBOOK_PROVIDER_EXCEPTION);
        }
    }


}