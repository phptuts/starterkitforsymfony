---
layout: page
title: "Forms"
category: common
date: 2017-07-22 15:12:31
order: 2
disqus: 1
---

We use forms the way recommended in the best practices.  Meaning we don't do any validation in our forms.  We use validation_groups and annotations to add validation to the form.  Thus our forms are only responsible for what is what data is being processed.


### [Forget Password Form]()

This is a form that uses data transformer on the whole form to transform the email address into a user object.  If the form fails it will put the form error on the email object.  This is done in the config with the "." for error_mapping.  You can read more about it [Error Mapping Symfony Forms](http://symfony.com/doc/current/reference/forms/types/form.html#error-mapping)

```
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
```

Also note that the [data transform](https://github.com/phptuts/starterkitforsymfony/blob/master/src/AppBundle/Form/DataTransformer/UserEmailTransformer.php) always returns a User object.  The reason is that it is transforming the whole form.

## [Change Password Form](https://github.com/phptuts/starterkitforsymfony/blob/master/src/AppBundle/Form/User/ChangePasswordType.php)

This form is injects the AuthorizationChecker which is used to see if the user is an admin.  If user is an admin we remove the current password field.  This allows admins to change other user's passwords.


```
// If the user is role admin entering a a password is not required
if ($this->authorizationChecker->isGranted('ROLE_ADMIN')) {
    $builder->remove('currentPassword');
}
```


Also note that the change password form uses a [ChangePasswordModel](https://github.com/phptuts/starterkitforsymfony/blob/master/src/AppBundle/Model/User/ChangePasswordModel.php) which has a [UserPassword](https://symfony.com/doc/current/reference/constraints/UserPassword.html) constraint.  This validator will match the password entered with the password the user is trying to change.

### Helpful Links

- [Form](http://symfony.com/doc/current/forms.html)
- [Data Transformers](http://symfony.com/doc/current/form/data_transformers.html)
- [AuthorizationChecker](http://symfony.com/doc/current/components/security/authorization.html#authorization-checker)
- [Symfony Validator](http://symfony.com/doc/current/validation.html)
- [Symfony Best Practices with Forms](https://symfony.com/doc/current/best_practices/forms.html)
