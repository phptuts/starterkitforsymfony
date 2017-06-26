<?php

namespace CoreBundle\Model\User;

use Symfony\Component\Validator\Constraints as Constraints;
use Symfony\Component\Security\Core\Validator\Constraints\UserPassword;

/**
 * Class ChangePasswordModel
 * @package CoreBundle\Model\User
 */
class ChangePasswordModel
{
    /**
     * @var string
     * @Constraints\Length(min=User::MIN_PASSWORD_LENGTH, max=User::MAX_PASSWORD_LENGTH)
     * @Constraints\NotBlank()
     */
    private $currentPassword;

    /**
     * @UserPassword(message="The password you entered does not match your current password.")
     * @Constraints\NotBlank()
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