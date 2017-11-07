<?php

namespace StarterKit\StartBundle\Form;

use StarterKit\StartBundle\Entity\BaseUser;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type as Types;

/**
 * Class ResetPasswordType
 * @package StarterKit\StartBundle\Form\User
 */
class ResetPasswordType extends AbstractType
{
    /**
     * @var string The fully qualified class name
     */
    private $userClass;

    public function __construct($userClass)
    {
        $this->userClass = $userClass;
    }

    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('plainPassword', Types\PasswordType::class);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        // We set the data class to user so we can use the validation attached to the entity
        // In the controller we just grab the password field from the form.
        $resolver->setDefaults([
            'data_class' => $this->userClass,
            'validation_groups' => [BaseUser::VALIDATION_GROUP_PLAIN_PASSWORD],
            'csrf_protection' => false,
        ]);
    }
}