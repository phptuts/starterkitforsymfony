<?php

namespace StarterKit\StartBundle\Tests\Service;


use PHPUnit\Framework\Assert;
use StarterKit\StartBundle\Service\FormSerializer;
use StarterKit\StartBundle\Tests\BaseTestCase;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Form;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Count;
use Symfony\Component\Validator\Constraints\Email;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Valid;

class FormSerializerTest extends BaseTestCase
{
    /**
     * @var FormSerializer
     */
    protected $formSerializer;


    public function setUp()
    {
        parent::setUp();
        $this->formSerializer = $this->getContainer()->get('StarterKit\StartBundle\Service\FormSerializer');

    }

    public function testEmptyForm()
    {
        $expectedJsonString = '{"errors":["The CSRF token is invalid. Please try to resubmit the form."],"children":{"name":{"errors":["This value should not be blank."]},"emails":{"errors":["This collection should contain 3 elements or more."]}}}';


        /** @var Form $form  */
        $form = $this->getContainer()->get('form.factory')->create(TestFormType::class);
        $form->submit([]);

        Assert::assertEquals($expectedJsonString, json_encode($this->formSerializer->createFormErrorArray($form)));
    }

    public function testCollectionFormErrors()
    {
        $data = ['emails' => [
                [
                    'email' => 'not_real_email',
                    'provider' => null,
                ],
            [
                'email' => 'real@gmail.com',
                'provider' => 'google.',
            ],
            ]
        ];

        /** @var Form $form  */
        $form = $this->getContainer()->get('form.factory')->create(TestFormType::class);
        $form->submit($data);

        $jsonString = '{"errors":[  
      "The CSRF token is invalid. Please try to resubmit the form."
   ],
   "children":{  
      "name":{  
         "errors":[  
            "This value should not be blank."
         ]
      },
      "emails":{  
         "errors":[  
            "This collection should contain 0 elements or less."
         ],
         "children":[  
            {  
               "children":{  
                  "provider":{  
                     "errors":[  
                        "This value should not be blank."
                     ]
                  },
                  "email":{  
                     "errors":[  
                        "This value is not a valid email address."
                     ]
                  }
               }
            },
            {  
               "children":{  
                  "provider":[  

                  ],
                  "email":[  

                  ]
               }
            }
         ]
      }
   }
}';

        $formErrors = $this->formSerializer->createFormErrorArray($form);
        Assert::assertEquals(json_decode($jsonString, true), $formErrors);

    }
}

class TestFormType extends AbstractType {

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('name', TextType::class, [
            'constraints' => [
                new NotBlank(),
                new Length(['min' => 3, 'max' => 30])
            ]
        ])->add('emails', CollectionType::class, [
            'constraints' => [
                new Count(['min' => 3, 'max' => 0]),
                new Valid()
            ],
            'entry_type' => DescriptionType::class,
            'allow_add' => true,
            'allow_delete' => true,
            'error_bubbling' => false
        ]);

    }
}

class DescriptionType extends AbstractType {

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('provider', TextType::class, [
            'constraints' => [
                new NotBlank(),
            ]
        ])
            ->add('email', TextType::class, [
                'constraints' => [
                    new NotBlank(),
                    new Email()
                ]
            ]);
    }
}