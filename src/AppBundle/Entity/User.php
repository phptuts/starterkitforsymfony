<?php

namespace AppBundle\Entity;

use StarterKit\StartBundle\Entity\BaseUser;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="StarterKit\StartBundle\Repository\UserRepository")
 * @ORM\Table(name="User")
 * @ORM\HasLifecycleCallbacks()
 *
 * Class User
 * @package AppBundle\Entity
 */
class User extends BaseUser
{

}