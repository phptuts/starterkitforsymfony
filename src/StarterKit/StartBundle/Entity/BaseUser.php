<?php

namespace StarterKit\StartBundle\Entity;

use StarterKit\StartBundle\Model\Response\ResponseTypeInterface;
use StarterKit\StartBundle\Model\Auth\AuthTokenModel;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Security\Core\User\AdvancedUserInterface;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Constraints;

/**
 * User
 *
 * @ORM\HasLifecycleCallbacks()
 * @ORM\MappedSuperclass()
 *
 * @UniqueEntity(fields={"email"}, groups={BaseUser::VALIDATION_GROUP_DEFAULT})
 * @UniqueEntity(fields={"displayName"}, groups={BaseUser::VALIDATION_GROUP_DEFAULT})
 *
 * @link http://symfony.com/doc/current/security/entity_provider.html
 */
abstract class BaseUser implements AdvancedUserInterface, ViewInterface
{

    use TimeStampTrait;

    /**
     * This is the default validation group used across all the user forms
     * @var string
     */
    const VALIDATION_GROUP_DEFAULT = "user_default_validation_group";

    /**
     * This is the default validation group used across all the user forms
     * @var string
     */
    const VALIDATION_IMAGE_REQUIRED = "user_image_required";


    /**
     * The validation group for plain password.
     * @var  string
     */
    const VALIDATION_GROUP_PLAIN_PASSWORD = "user_plain_password";


    /**
     * This is the min password length
     * @var string
     */
    const MIN_PASSWORD_LENGTH = 3;

    /**
     * This is max password length
     * @var string
     */
    const MAX_PASSWORD_LENGTH = 128;

    /**
     * The Response type for the entity
     *
     * @var string
     */
    const RESPONSE_TYPE = 'user';

    /**
     * @var int
     *
     * @ORM\Column(name="id", type="string")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="UUID")
     */
    protected $id;

    /**
     * @var string
     *
     * @Constraints\Length(min="5", max="100", groups={BaseUser::VALIDATION_GROUP_DEFAULT})
     * @ORM\Column(name="display_name", type="string", length=255, nullable=true, unique=true)
     */
    protected $displayName;

    /**
     * @var string
     *
     * @Constraints\NotBlank(groups={BaseUser::VALIDATION_GROUP_DEFAULT})
     * @Constraints\Email(groups={BaseUser::VALIDATION_GROUP_DEFAULT})
     * @ORM\Column(name="email", type="string", length=255, unique=true)
     */
    protected $email;


    /**
     * This would be something like their facebook user id
     *
     * @var string
     *
     * @ORM\Column(name="facebook_user_id", type="string", nullable=true, unique=true)
     */
    protected $facebookUserId;

    /**
     * This would be something like their google user id
     *
     * @var string
     *
     * @ORM\Column(name="google_user_id", type="string", nullable=true, unique=true)
     */
    protected $googleUserId;

    /**
     * @var string
     *
     * @ORM\Column(name="forget_password_token", type="string", nullable=true)
     */
    protected $forgetPasswordToken;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="forget_password_expired", type="datetime", nullable=true)
     */
    protected $forgetPasswordExpired;

    /**
     * @var string
     *
     * @ORM\Column(name="image_url", type="string", length=255, nullable=true)
     */
    protected $imageUrl;

    /**
     * @var string
     *
     * @ORM\Column(name="password", type="string", length=255)
     */
    protected $password;

    /**
     * @var array
     *
     * @ORM\Column(name="roles", type="json_array")
     */
    protected $roles;

    /**
     * @var string
     *
     * @Constraints\Length(max="3000", groups={BaseUser::VALIDATION_GROUP_DEFAULT})
     * @ORM\Column(name="bio", type="text", nullable=true)
     */
    protected $bio;

    /**
     * @var boolean
     *
     * @ORM\Column(name="enabled", type="boolean")
     */
    protected $enabled;

    /**
     * @var string
     *
     * @Constraints\NotBlank(groups={BaseUser::VALIDATION_GROUP_PLAIN_PASSWORD})
     * @Constraints\Length(max=BaseUser::MAX_PASSWORD_LENGTH, min=BaseUser::MIN_PASSWORD_LENGTH, groups={BaseUser::VALIDATION_GROUP_PLAIN_PASSWORD})
     */
    protected $plainPassword;

    /**
     * @var string
     *
     * @ORM\Column(name="source", type="string")
     */
    protected $source;

    /**
     * @var UploadedFile
     *
     * @Constraints\NotBlank(groups={BaseUser::VALIDATION_IMAGE_REQUIRED})
     * @Constraints\Image(maxSize="7Mi", mimeTypes={"image/gif", "image/jpeg", "image/png"}, groups={BaseUser::VALIDATION_GROUP_DEFAULT})
     */
    protected $image;

    /**
     * @var string
     * @ORM\Column(name="refresh_token", type="string", nullable=true)
     */
    protected $refreshToken;

    /**
     * @var \DateTime
     * @ORM\Column(name="refresh_token_expire", type="datetime", nullable=true)
     */
    protected $refreshTokenExpire;

    /**
     * Get id
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }


    /**
     * Set email
     *
     * @param string $email
     *
     * @return BaseUser
     */
    public function setEmail($email)
    {
        $this->email = $email;

        return $this;
    }

    /**
     * Get email
     *
     * @return string
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * Set imageUrl
     *
     * @param string $imageUrl
     *
     * @return BaseUser
     */
    public function setImageUrl($imageUrl)
    {
        $this->imageUrl = $imageUrl;

        return $this;
    }

    /**
     * Get imageUrl
     *
     * @return string
     */
    public function getImageUrl()
    {
        return $this->imageUrl;
    }

    /**
     * Set password
     *
     * @param string $password
     *
     * @return BaseUser
     */
    public function setPassword($password)
    {
        $this->password = $password;

        return $this;
    }

    /**
     * Get password
     *
     * @return string
     */
    public function getPassword()
    {
        return $this->password;
    }

    /**
     * Set roles
     *
     * @param array $roles
     *
     * @return BaseUser
     */
    public function setRoles($roles)
    {
        $this->roles = $roles;

        return $this;
    }

    /**
     * Get roles
     *
     * @return array
     */
    public function getRoles()
    {
        if (empty($this->roles)) {
            return ['ROLE_USER'];
        }

        return $this->roles;
    }

    /**
     * Returns true if the user has the role
     *
     * @param string $role
     * @return bool
     */
    public function hasRole($role)
    {
        return in_array($role, $this->getRoles());
    }

    /**
     * Set bio
     *
     * @param string $bio
     *
     * @return BaseUser
     */
    public function setBio($bio)
    {
        $this->bio = $bio;

        return $this;
    }

    /**
     * Get bio
     *
     * @return string
     */
    public function getBio()
    {
        return $this->bio;
    }

    /**
     * @return bool
     */
    public function isEnabled()
    {
        return $this->enabled;
    }

    /**
     * @param bool $enabled
     * @return BaseUser
     */
    public function setEnabled($enabled)
    {
        $this->enabled = $enabled;

        return $this;
    }

    /**
     * @return string
     */
    public function getDisplayName()
    {
        return $this->displayName;
    }

    /**
     * @param string $displayName
     * @return BaseUser
     */
    public function setDisplayName($displayName)
    {
        $this->displayName = $displayName;

        return $this;
    }

    /**
     * @return string
     */
    public function getForgetPasswordToken()
    {
        return $this->forgetPasswordToken;
    }

    /**
     * @param string $forgetPasswordToken
     * @return BaseUser
     */
    public function setForgetPasswordToken($forgetPasswordToken)
    {
        $this->forgetPasswordToken = $forgetPasswordToken;

        return $this;
    }

    /**
     * @return \DateTime
     */
    public function getForgetPasswordExpired()
    {
        return $this->forgetPasswordExpired;
    }

    /**
     * @param \DateTime $forgetPasswordExpired
     * @return BaseUser
     */
    public function setForgetPasswordExpired($forgetPasswordExpired)
    {
        $this->forgetPasswordExpired = $forgetPasswordExpired;

        return $this;
    }

    /**
     * @return UploadedFile
     */
    public function getImage()
    {
        return $this->image;
    }

    /**
     * @param UploadedFile $image
     * @return BaseUser
     */
    public function setImage($image)
    {
        $this->image = $image;

        return $this;
    }

    /**
     * @return string
     */
    public function getSource()
    {
        return $this->source;
    }

    /**
     * @param string $source
     * @return BaseUser
     */
    public function setSource($source)
    {
        $this->source = $source;

        return $this;
    }

    /**
     * Returns the username used to authenticate the user.
     *
     * @return string The username
     */
    public function getUsername()
    {
        // We use the email address because we want the user to authenticated by email
        return $this->email;
    }


    /**
     * @return string
     */
    public function getPlainPassword()
    {
        return $this->plainPassword;
    }

    /**
     * @param string $plainPassword
     * @return BaseUser
     */
    public function setPlainPassword($plainPassword)
    {
        $this->plainPassword = $plainPassword;

        return $this;
    }

    /**
     * We are returning null because we are using bcrypt
     * @return null
     */
    public function getSalt()
    {
        return null;
    }

    /**
     * Clears out the the plain password so it never get serialized
     */
    public function eraseCredentials()
    {
        $this->plainPassword = null;
    }

    /**
     * Checks whether the user"s account has expired.
     *
     * Internally, if this method returns false, the authentication system
     * will throw an AccountExpiredException and prevent login.
     *
     * @return bool true if the user"s account is non expired, false otherwise
     *
     * @see AccountExpiredException
     */
    public function isAccountNonExpired()
    {
        return true;
    }

    /**
     * Checks whether the user is locked.
     *
     * Internally, if this method returns false, the authentication system
     * will throw a LockedException and prevent login.
     *
     * @return bool true if the user is not locked, false otherwise
     *
     * @see LockedException
     */
    public function isAccountNonLocked()
    {
        return true;
    }

    /**
     * Checks whether the user"s credentials (password) has expired.
     *
     * Internally, if this method returns false, the authentication system
     * will throw a CredentialsExpiredException and prevent login.
     *
     * @return bool true if the user"s credentials are non expired, false otherwise
     *
     * @see CredentialsExpiredException
     */
    public function isCredentialsNonExpired()
    {
        return true;
    }

    /**
     * @return string
     */
    public function getFacebookUserId()
    {
        return $this->facebookUserId;
    }

    /**
     * @param string $facebookUserId
     * @return BaseUser
     */
    public function setFacebookUserId($facebookUserId)
    {
        $this->facebookUserId = $facebookUserId;

        return $this;
    }

    /**
     * @return string
     */
    public function getGoogleUserId()
    {
        return $this->googleUserId;
    }

    /**
     * @param string $googleUserId
     * @return BaseUser
     */
    public function setGoogleUserId($googleUserId)
    {
        $this->googleUserId = $googleUserId;

        return $this;
    }

    /**
     * @return string
     */
    public function getRefreshToken()
    {
        return $this->refreshToken;
    }

    /**
     * @param string $refreshToken
     * @return BaseUser
     */
    public function setRefreshToken($refreshToken)
    {
        $this->refreshToken = $refreshToken;

        return $this;
    }

    /**
     * @return \DateTime
     */
    public function getRefreshTokenExpire()
    {
        return $this->refreshTokenExpire;
    }

    /**
     * @param \DateTime $refreshTokenExpire
     * @return BaseUser
     */
    public function setRefreshTokenExpire($refreshTokenExpire)
    {
        $this->refreshTokenExpire = $refreshTokenExpire;

        return $this;
    }

    /**
     * Returns an auth model representing the token
     *
     * @return AuthTokenModel
     */
    public function getAuthRefreshModel()
    {
        return new AuthTokenModel($this->refreshToken, $this->refreshTokenExpire->getTimestamp());
    }

    /**
     * Returns true if the refresh token is valid
     *
     * @return bool
     */
    public function isRefreshTokenValid()
    {
        return !empty($this->getRefreshToken()) && $this->getRefreshTokenExpire() > new \DateTime();
    }

    /**
     * Returns an array of hte view for displaying in a list
     *
     * @return array
     */
    public function listView()
    {
        return [
            'id' => $this->getId(),
            'displayName' => $this->getDisplayName(),
            'imageUrl' => $this->getImageUrl(),
        ];
    }

    /**
     * Return an array of the view for displaying as a single item
     *
     * @return array
     */
    public function singleView()
    {
        return [
            'id' => $this->getId(),
            'displayName' => $this->getDisplayName(),
            'roles' => $this->getRoles(),
            'imageUrl' => $this->getImageUrl(),
            'email' => $this->getEmail(),
            'bio' => $this->getBio()
        ];
    }

    /**
     * Returns the data to merge into the jwt token payload
     *
     * @return array
     */
    public function getJWTPayload()
    {
        return [
            'displayName' => $this->getDisplayName(),
            'roles' => $this->getRoles(),
            'imageUrl' => $this->getImageUrl(),
            'email' => $this->getEmail(),
            'bio' => $this->getBio()
        ];
    }
}

