<?php

namespace CoreBundle\Form\User;

use CoreBundle\Model\User\ChangePasswordModel;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Security\Core\Authorization\AuthorizationChecker;
use Symfony\Component\Form\Extension\Core\Type as Types;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;

/**
 * Class ChangePasswordType
 * @package CoreBundle\Form\User
 */
class ChangePasswordType extends AbstractType
{
    /**
     * @var AuthorizationChecker
     */
    protected $authorizationChecker;

    public function __construct(AuthorizationCheckerInterface $authorizationChecker)
    {
        $this->authorizationChecker = $authorizationChecker;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('new_password', Types\PasswordType::class)
            ->add('current_password', Types\PasswordType::class);

        // If the user is role admin entering a a password is not required
        if ($this->authorizationChecker->isGranted('ROLE_ADMIN')) {
            $builder->remove('current_password');
        }
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => ChangePasswordModel::class
        ]);
    }
}