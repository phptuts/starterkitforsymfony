<?php


namespace AppBundle\Entity;

use AppBundle\Model\Security\AuthTokenModel;
use Doctrine\ORM\Mapping as ORM;

/**
 * Color
 * @ORM\Table(name="RefreshToken")
 * @ORM\HasLifecycleCallbacks()
 * @ORM\Entity(repositoryClass="AppBundle\Repository\RefreshTokenRepository")
 */
class RefreshToken
{

    use TimeStampTrait;

    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="token", type="string")
     */
    private $token;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="expires", type="datetime")
     */
    private $expires;

    /**
     * @var boolean
     *
     * @ORM\Column(name="used", type="boolean")
     */
    private $used = false;

    /**
     * @var User
     * @ORM\ManyToOne(targetEntity="User")
     * @ORM\JoinColumn(name="user_id", referencedColumnName="id", onDelete="CASCADE", nullable=false)
     */
    private $user;

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getToken()
    {
        return $this->token;
    }

    /**
     * @param string $token
     * @return RefreshToken
     */
    public function setToken($token)
    {
        $this->token = $token;

        return $this;
    }

    /**
     * @return \DateTime
     */
    public function getExpires()
    {
        return $this->expires;
    }

    /**
     * @param \DateTime $expires
     *
     * @return RefreshToken
     */
    public function setExpires($expires)
    {
        $this->expires = $expires;

        return $this;
    }

    /**
     * @return bool
     */
    public function isUsed()
    {
        return $this->used;
    }

    /**
     * @param bool $used
     *
     * @return RefreshToken
     */
    public function setUsed($used)
    {
        $this->used = $used;

        return $this;
    }

    /**
     * @return User
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * @param User $user
     *
     * @return RefreshToken
     */
    public function setUser($user)
    {
        $this->user = $user;

        return $this;
    }

    /**
     * Returns an auth model representing the token
     *
     * @return AuthTokenModel
     */
    public function getAuthModel()
    {
        return new AuthTokenModel($this->token, $this->expires->getTimestamp());
    }
}