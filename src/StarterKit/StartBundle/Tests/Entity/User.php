<?php


namespace StarterKit\StartBundle\Tests\Entity;

use StarterKit\StartBundle\Entity\BaseUser;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="StarterKit\StartBundle\Repository\UserRepository")
 * @ORM\Table(name="TestUser")
 * @ORM\HasLifecycleCallbacks()
 *
 * Class User
 * @package StarterKit\StartBundle\Tests\Entity
 */
class User extends BaseUser
{

}