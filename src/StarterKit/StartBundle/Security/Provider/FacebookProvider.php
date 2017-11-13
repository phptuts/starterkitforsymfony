<?php

namespace StarterKit\StartBundle\Security\Provider;

use Facebook\Facebook;
use StarterKit\StartBundle\Entity\BaseUser;
use StarterKit\StartBundle\Exception\ProgrammerException;
use StarterKit\StartBundle\Factory\FaceBookClientFactoryInterface;
use StarterKit\StartBundle\Service\UserService;
use Facebook\Exceptions\FacebookResponseException;
use Facebook\Exceptions\FacebookSDKException;
use StarterKit\StartBundle\Service\UserServiceInterface;
use Symfony\Component\Security\Core\Exception\UsernameNotFoundException;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * Class FacebookProvider
 * @package StarterKit\StartBundle\Security\Provider
 */
class FacebookProvider implements FacebookProviderInterface
{
    use CustomProviderTrait;

    /**
     * @var Facebook
     */
    protected $facebookClient;


    public function __construct(
        FaceBookClientFactoryInterface $faceBookClientFactory,
        UserServiceInterface $userService)
    {
        $this->facebookClient = $faceBookClientFactory->getClient();
        $this->userService = $userService;
    }

    /**
     * 1) We validate the access token by fetching the user information
     * 2) We search for facebook user id and if one is found we return that user
     * 3) Then we search by email and if one is found we update the user to have that facebook user id
     * 4) If nothing is found we register the user with facebook user id
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
            $facebookUser = $response->getGraphUser();
            $email = $facebookUser->getEmail();
            $facebookUserId = $facebookUser->getId();
            $user = $this->userService->findByFacebookUserId($facebookUserId);

            // We always check their facebook user id first because they could have change their email address
            if (!empty($user)) {
                return $user;
            }

            $user = $this->userService->findUserByEmail($email);

            // This means that user already register and we need to associate their facebook account to their user entity
            if (!empty($user)) {
                $this->updateUserWithFacebookId($user, $facebookUserId);
                return $user;
            }
            
            // This means no user was found and we need to register user with their facebook user id
            return $this->registerUser($email, $facebookUserId);
        } catch (FacebookResponseException $ex) {
            throw new UsernameNotFoundException("Facebook AuthToken Did Not validate, ERROR MESSAGE " . $ex->getMessage(), ProgrammerException::FACEBOOK_RESPONSE_EXCEPTION_CODE);
        } catch (FacebookSDKException $ex) {
            throw new UsernameNotFoundException("Facebook SDK failed, ERROR MESSAGE " . $ex->getMessage(), ProgrammerException::FACEBOOK_SDK_EXCEPTION_CODE);
        } catch (\Exception $ex) {
            throw new UsernameNotFoundException("Something unknown went wrong, ERROR MESSAGE  " . $ex->getMessage(), ProgrammerException::FACEBOOK_PROVIDER_EXCEPTION);
        }
    }

    /**
     * Updates the user with their facebook id creating a link between their facebook account and user information.
     *
     * @param BaseUser $user
     * @param string $facebookUserId
     */
    protected function updateUserWithFacebookId(BaseUser $user, $facebookUserId)
    {
        $user->setFacebookUserId($facebookUserId);
        $this->userService->save($user);
    }

    /**
     * We register the user with their facebook id and email.
     *
     * @param string $email
     * @param string $facebookUserId
     * @return BaseUser
     */
    protected function registerUser($email, $facebookUserId)
    {
        $className = $this->userService->getUserClass();
        /** @var BaseUser $user */
        $user = (new  $className());
        $user->setEmail($email)
            ->setFacebookUserId($facebookUserId)
            ->setPlainPassword(base64_encode(random_bytes(20)));
        $this->userService->registerUser($user, UserService::SOURCE_TYPE_FACEBOOK);

        return $user;
    }
}
