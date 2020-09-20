<?php

declare(strict_types=1);

namespace Appyfurious\AdminUserBundle\Form;

use Appyfurious\AdminUserBundle\Entity\AdminUser;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ResettingPassType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder->add('password', RepeatedType::class, array(
            'type' => PasswordType::class,
            'options' => array(
                'translation_domain' => 'translation',
                'attr' => array(
                    'autocomplete' => 'new-password',
                ),
            ),
            'first_options' => array('label' => 'form.new_password'),
            'second_options' => array('label' => 'form.new_password_confirmation')
        ));
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults(array(
            'data_class' => AdminUser::class,
            'csrf_token_id' => 'resetting',
        ));
    }

    public function getBlockPrefix(): string
    {
        return 'resetting';
    }
}
