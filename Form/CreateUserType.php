<?php

declare(strict_types=1);

namespace Appyfurious\AdminUserBundle\Form;

use Appyfurious\AdminUserBundle\Entity\AdminUser;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\NotNull;

class CreateUserType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder->add('email', TextType::class, [
            'required' => true,
            'label' => 'Email',
            'attr' => [
                'autocomplete' => 'off'
            ],
            'constraints' => [
                new NotNull(),
                new NotBlank()
            ]
        ]);

        $builder->add('username', TextType::class, [
            'required' => true,
            'label' => 'Username',
            'attr' => [
                'autocomplete' => 'off'
            ],
            'constraints' => [
                new NotNull(),
                new NotBlank()
            ]
        ]);
    }

    public function getBlockPrefix(): string
    {
        return 'create';
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults(array(
            'data_class' => AdminUser::class
        ));
    }
}