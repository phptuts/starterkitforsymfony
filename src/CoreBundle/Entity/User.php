<?php

namespace CoreBundle\Entity;

use CoreBundle\Model\Response\ResponseTypeInterface;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Security\Core\User\AdvancedUserInterface;
use Symfony\Component\Security\Core\User\EquatableInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Validator\Constraints as Constraints;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use JMS\Serializer\Annotation as Serializer;

/**
 * User
 * @link http://symfony.com/doc/current/security/entity_provider.html
 * @ORM\Table(name="User")
 * @ORM\Entity(repositoryClass="CoreBundle\Repository\UserRepository")
 * @UniqueEntity(fields={"email"}, groups={User::VALIDATION_GROUP_DEFAULT})
 * @UniqueEntity(fields={"displayName"}, groups={User::VALIDATION_GROUP_DEFAULT})
 * @Serializer\ExclusionPolicy("ALL")
 */
class User implements AdvancedUserInterface, \Serializable, EquatableInterface, ResponseTypeInterface
{

    /**
     * This is the default validation group used across all the user forms
     * @var string
     */
    const VALIDATION_GROUP_DEFAULT = "user_default_validation_group";

    /**
     * The validation group for plain password.
     * @var  string
     */
    const VALIDATION_GROUP_PLAIN_PASSWORD = "user_plain_password";

    /**
     * This serialization group exposes users personal information like email
     *
     * @var string
     */
    const USER_PERSONAL_SERIALIZATION_GROUP = 'users';

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
     * @var int
     *
     * @Serializer\Expose()
     *
     * @ORM\Column(name="id", type="string")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="UUID")
     */
    protected $id;

    /**
     * @var string
     *
     * @Serializer\Expose()
     *
     * @Constraints\Length(min="5", max="100", groups={User::VALIDATION_GROUP_DEFAULT})
     * @ORM\Column(name="display_name", type="string", length=255, nullable=true, unique=true)
     */
    protected $displayName;

    /**
     * @var string
     *
     * @Serializer\Expose()
     * @Serializer\Groups({User::USER_PERSONAL_SERIALIZATION_GROUP})
     *
     * @Constraints\NotBlank(groups={User::VALIDATION_GROUP_DEFAULT})
     * @Constraints\Email(groups={User::VALIDATION_GROUP_DEFAULT})
     * @ORM\Column(name="email", type="string", length=255, unique=true)
     */
    protected $email;

    /**
     * @var string
     *
     * @Serializer\Exclude()
     *
     * @ORM\Column(name="forget_password_token", type="string", nullable=true)
     */
    protected $forgetPasswordToken;

    /**
     * @var \DateTime
     *
     * @Serializer\Exclude()
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
     * @Serializer\Exclude()
     *
     * @ORM\Column(name="password", type="string", length=255)
     */
    protected $password;

    /**
     * @var array
     *
     *
     *
     * @ORM\Column(name="roles", type="json_array")
     */
    protected $roles;

    /**
     * @var string
     *
     * @Serializer\Expose()
     *
     * @Constraints\Length(max="3000", groups={User::VALIDATION_GROUP_DEFAULT})
     * @ORM\Column(name="bio", type="text", nullable=true)
     */
    protected $bio;

    /**
     * @var boolean
     *
     * @Serializer\Exclude()
     *
     * @ORM\Column(name="enabled", type="boolean")
     */
    protected $enabled;

    /**
     * @var string
     *
     * @Serializer\Exclude()
     *
     * @Constraints\NotBlank(groups={User::VALIDATION_GROUP_PLAIN_PASSWORD})
     * @Constraints\Length(max=User::MAX_PASSWORD_LENGTH, min=User::MIN_PASSWORD_LENGTH, groups={User::VALIDATION_GROUP_PLAIN_PASSWORD})
     */
    protected $plainPassword;

    /**
     * @var string
     * @Serializer\Exclude()
     *
     * @ORM\Column(name="source", type="string")
     */
    protected $source;

    /**
     * @var UploadedFile
     *
     * @Serializer\Exclude()
     * // This is better to test end to end
     * @Constraints\Image(maxSize="7Mi", mimeTypes={"image/gif", "image/jgp", "image/png"}, groups={User::VALIDATION_GROUP_DEFAULT})
     */
    protected $image;

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
     * @return User
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
     * @return User
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
     * @return User
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
     * @return User
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
        return in_array($role, $this->roles);
    }

    /**
     * Set bio
     *
     * @param string $bio
     *
     * @return User
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
     * @return User
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
     * @return User
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
     * @return User
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
     * @return User
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
     * @return User
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
     * @return User
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
     * @return User
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
     * String representation of object
     * @link http://php.net/manual/en/serializable.serialize.php
     * @return string the string representation of the object or null
     * @since 5.1.0
     */
    public function serialize()
    {
        return serialize([
            $this->enabled,
            $this->email,
            $this->id
        ]);
    }

    /**
     * Constructs the object
     * @link http://php.net/manual/en/serializable.unserialize.php
     * @param string $serialized <p>
     * The string representation of the object.
     * </p>
     * @return void
     * @since 5.1.0
     */
    public function unserialize($serialized)
    {
        list (
            $this->enabled,
            $this->email,
            $this->id
            ) = unserialize($serialized);
    }

    /**
     * The equality comparison should neither be done by referential equality
     * nor by comparing identities (i.e. getId() === getId()).
     *
     * However, you do not need to compare every attribute, but only those that
     * are relevant for assessing whether re-authentication is required.
     *
     * Also implementation should consider that $user instance may implement
     * the extended user interface `AdvancedUserInterface`.
     *
     * @param UserInterface $user
     *
     * @return bool
     */
    public function isEqualTo(UserInterface $user)
    {
        // This is using email address to compare.
        return $this->getUsername() == $user->getUsername();
    }

    /**
     * Returns the type of response being serialized
     *
     * @return string
     */
    public function getResponseType()
    {
        return 'users';
    }


}

