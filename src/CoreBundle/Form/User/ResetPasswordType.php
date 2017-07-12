<?php

namespace CoreBundle\Form\User;

use CoreBundle\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type as Types;

/**
 * Class ResetPasswordType
 * @package CoreBundle\Form\User
 */
class ResetPasswordType extends AbstractType
{
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
            'data_class' => User::class,
            'validation_groups' => [User::VALIDATION_GROUP_PLAIN_PASSWORD]
        ]);
    }
}