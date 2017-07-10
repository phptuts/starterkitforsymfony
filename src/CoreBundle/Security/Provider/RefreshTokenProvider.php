<?php


namespace CoreBundle\Security\Provider;


use CoreBundle\Entity\RefreshToken;
use CoreBundle\Exception\ProgrammerException;
use CoreBundle\Repository\RefreshTokenRepository;
use CoreBundle\Repository\UserRepository;
use CoreBundle\Service\Credential\RefreshTokenService;
use Symfony\Component\Security\Core\Exception\UsernameNotFoundException;

class RefreshTokenProvider extends AbstractCustomProvider
{
    /**
     * @var RefreshTokenRepository
     */
    private $refreshTokenRepository;

    /**
     * @var RefreshTokenService
     */
    private $refreshTokenService;

    /**
     * RefreshTokenProvider constructor.
     * @param UserRepository $userRepository
     * @param RefreshTokenRepository $refreshTokenRepository
     * @param RefreshTokenService $refreshTokenService
     */
    public function __construct(UserRepository $userRepository,
                                RefreshTokenRepository $refreshTokenRepository,
                                RefreshTokenService $refreshTokenService
    )
    {
        parent::__construct($userRepository);
        $this->refreshTokenRepository = $refreshTokenRepository;
        $this->refreshTokenService = $refreshTokenService;
    }

    /**
     * See if the refresh token is valid and if it is it return the user otherwise we throw the UsernameNotFoundException
     *
     * @param string $username the refresh token
     * @return \CoreBundle\Entity\User
     */
    public function loadUserByUsername($username)
    {
        try{
            $token = $this->refreshTokenRepository->getValidRefreshToken($username);
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