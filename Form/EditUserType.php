<?php

declare(strict_types=1);

namespace Appyfurious\AdminUserBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Appyfurious\AdminUserBundle\Entity\AdminUser;

class EditUserType extends AbstractType
{
    private array $roles;

    public function __construct(array $roles)
    {
        $this->roles = $roles;
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder->add('username', TextType::class, [
            'label' => 'form.username',
            'translation_domain' => 'translation',
        ]);
        $builder->add('email', EmailType::class, [
            'label' => 'form.email',
            'translation_domain' => 'translation',
        ]);
        $builder->add('enabled', CheckboxType::class, [
            'label' => 'form.enabled',
            'translation_domain' => 'translation',
            'required' => false,
        ]);
        $builder->add('roles', ChoiceType::class, [
            'label' => 'form.roles',
            'translation_domain' => 'translation',
            'multiple' => true,
            'expanded' => true,
            'choices' => array_keys($this->roles),
            'choice_label' => function ($value) { return $value; },
        ]);
    }

    public function getBlockPrefix(): string
    {
        return 'edit';
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults(array(
            'data_class' => AdminUser::class
        ));
    }
}
