<?php

namespace CoreBundle\Form\User;

use CoreBundle\Entity\User;
use CoreBundle\Form\DataTransformer\UserEmailTransformer;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type as Types;

/**
 * Class ForgetPasswordType
 * @package CoreBundle\Form\User
 */
class ForgetPasswordType extends AbstractType
{
    /**
     * @var UserEmailTransformer
     */
    private $emailTransformer;

    public function __construct(UserEmailTransformer $emailTransformer)
    {
        $this->emailTransformer = $emailTransformer;
    }

    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('email', Types\EmailType::class);

        // We transform the whole form into a user object using the email field
        $builder->addModelTransformer($this->emailTransformer);
    }

    /**
     * This will configure the form so all error / data transform failure go to the email field
     * We want this form getData method to return user, so that it's easier to deal with in the controller
     * @link http://symfony.com/doc/current/reference/forms/types/form.html#error-mapping
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => User::class,
            // This is what maps the error to the user
            'error_mapping' => [
                '.' => 'email'
            ],
            'invalid_message' => 'Email was not found.',
            'validation_groups' => [User::VALIDATION_GROUP_DEFAULT]
        ]);
    }
}