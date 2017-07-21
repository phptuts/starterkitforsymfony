<?php

namespace AppBundle\Form\User;

use AppBundle\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type as Types;

/**
 * Class UpdateUserType
 * @package AppBundle\Form\User
 */
class UpdateUserType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('email', Types\EmailType::class)
            ->add('displayName', Types\TextType::class)
            ->add('image', Types\FileType::class)
            ->add('bio', Types\TextareaType::class);

        if ($options['api']) {
            $builder->remove('image');
        }
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => User::class,
            'validation_groups' => [User::VALIDATION_GROUP_DEFAULT],
            'api' => false
        ]);
    }
}