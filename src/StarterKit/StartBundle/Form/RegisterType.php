<?php

namespace StarterKit\StartBundle\Form;


use StarterKit\StartBundle\Entity\BaseUser;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type as Types;

/**
 * Class RegisterType
 * @package StarterKit\StartBundle\Form\User
 */
class RegisterType extends AbstractType
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
        $builder
            ->add('email', Types\EmailType::class)
            ->add('plainPassword', Types\PasswordType::class);
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => $this->userClass,
            'validation_groups' => [ BaseUser::VALIDATION_GROUP_PLAIN_PASSWORD, BaseUser::VALIDATION_GROUP_DEFAULT,],
            'csrf_protection' => false,
        ]);
    }
}