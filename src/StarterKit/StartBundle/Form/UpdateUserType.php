<?php

namespace StarterKit\StartBundle\Form;

use StarterKit\StartBundle\Entity\BaseUser;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type as Types;

/**
 * Class UpdateUserType
 * @package StarterKit\StartBundle\Form\User
 */
class UpdateUserType extends AbstractType
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
            'data_class' => $this->userClass,
            'validation_groups' => [BaseUser::VALIDATION_GROUP_DEFAULT],
            'api' => false,
            'csrf_protection' => false,
        ]);
    }
}