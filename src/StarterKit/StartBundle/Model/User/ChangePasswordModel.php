<?php

namespace StarterKit\StartBundle\Model\User;

use Symfony\Component\Validator\Constraints as Constraints;
use Symfony\Component\Security\Core\Validator\Constraints\UserPassword;
use StarterKit\StartBundle\Entity\BaseUser;

/**
 * Class ChangePasswordModel
 * @package StarterKit\StartBundle\Model\User
 */
class ChangePasswordModel
{
    /**
     * @var string
     * @UserPassword(message="The password you entered does not match your current password.")
     * @Constraints\NotBlank()
     */
    private $currentPassword;

    /**
     * @Constraints\NotBlank()
     * @Constraints\Length(min=BaseUser::MIN_PASSWORD_LENGTH, max=BaseUser::MAX_PASSWORD_LENGTH)
     * @var string
     */
    private $newPassword;

    /**
     * @return string
     */
    public function getCurrentPassword()
    {
        return $this->currentPassword;
    }

    /**
     * @param string $currentPassword
     * @return ChangePasswordModel
     */
    public function setCurrentPassword($currentPassword)
    {
        $this->currentPassword = $currentPassword;

        return $this;
    }

    /**
     * @return string
     */
    public function getNewPassword()
    {
        return $this->newPassword;
    }

    /**
     * @param string $newPassword
     * @return ChangePasswordModel
     */
    public function setNewPassword($newPassword)
    {
        $this->newPassword = $newPassword;

        return $this;
    }


}