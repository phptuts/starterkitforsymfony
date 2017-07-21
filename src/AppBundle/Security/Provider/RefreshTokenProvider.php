<?php


namespace AppBundle\Security\Provider;

use AppBundle\Entity\RefreshToken;
use AppBundle\Exception\ProgrammerException;
use AppBundle\Service\Credential\RefreshTokenService;
use AppBundle\Service\User\UserService;
use Symfony\Component\Security\Core\Exception\UsernameNotFoundException;

/**
 * Class RefreshTokenProvider
 * @package AppBundle\Security\Provider
 */
class RefreshTokenProvider extends AbstractCustomProvider
{

    /**
     * @var RefreshTokenService
     */
    private $refreshTokenService;

    /**
     * RefreshTokenProvider constructor.
     * @param UserService $userService
     * @param RefreshTokenService $refreshTokenService
     */
    public function __construct(UserService $userService,
                                RefreshTokenService $refreshTokenService
    )
    {
        parent::__construct($userService);
        $this->refreshTokenService = $refreshTokenService;
    }

    /**
     * See if the refresh token is valid and if it is it return the user otherwise we throw the UsernameNotFoundException
     *
     * @param string $username the refresh token
     * @return \AppBundle\Entity\User
     */
    public function loadUserByUsername($username)
    {
        try{
            $token = $this->refreshTokenService->getValidRefreshToken($username);
        }
        catch (ProgrammerException $ex) {
            throw new UsernameNotFoundException($ex->getMessage(), ProgrammerException::REFRESH_TOKEN_DUPLICATE);
        }

        if (empty($token)) {
            throw new UsernameNotFoundException("Invalid Refresh Token");
        }

        $this->saveRefreshTokenUsed($token);

        return $token->getUser();
    }

    /**
     * Sets the refresh token to used and saves it to the database.
     *
     * @param RefreshToken $token
     */
    private function saveRefreshTokenUsed(RefreshToken $token)
    {
        $token->setUsed(true);
        $this->refreshTokenService->save($token);
    }

}