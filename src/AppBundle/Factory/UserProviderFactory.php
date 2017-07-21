<?php


namespace AppBundle\Factory;

use AppBundle\Security\Guard\Token\AbstractTokenGuard;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\Intl\Exception\NotImplementedException;
use Symfony\Component\Security\Core\User\UserProviderInterface;

/**
 * Class SocialUserProviderFactory
 * @package AppBundle\Factory
 */
class UserProviderFactory
{
    /**
     * @var ContainerInterface
     */
    private $container;

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    /**
     * Gets a third party auth provider based on type
     *
     * @param string $type
     * @return UserProviderInterface|null
     */
    public function getUserProvider($type)
    {
        if ($type == AbstractTokenGuard::TOKEN_TYPE_FACEBOOK) {
            return $this->container->get('startsymfony.core.security.facebook_provider');
        }

        if ($type == AbstractTokenGuard::TOKEN_TYPE_GOOGLE) {
            return $this->container->get('startsymfony.core.security.google_provider');
        }

        if ($type == AbstractTokenGuard::TOKEN_TYPE_REFRESH) {
            return $this->container->get('startsymfony.core.security.refresh_token_provider');
        }

        if ($type == AbstractTokenGuard::TOKEN_TYPE_API) {
            return $this->container->get('startsymfony.core.security.token_provider');
        }

        throw new NotImplementedException(sprintf("The '%s' social user provider has not been implemented.", $type));
    }
}