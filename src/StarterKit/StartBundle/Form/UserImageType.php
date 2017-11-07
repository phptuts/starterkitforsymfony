<?php

namespace StarterKit\StartBundle\Form;

use StarterKit\StartBundle\Entity\BaseUser;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type as Types;

class UserImageType extends AbstractType
{
    /**
     * @var string The fully qualified class name
     */
    private $userClass;

    public function __construct($userClass)
    {
        $this->userClass = $userClass;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('image', Types\FileType::class);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => $this->userClass,
            'validation_groups' => [BaseUser::VALIDATION_GROUP_DEFAULT, BaseUser::VALIDATION_IMAGE_REQUIRED],
            'csrf_protection' => false,
        ]);
    }
}