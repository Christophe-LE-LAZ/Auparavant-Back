<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;

class UserType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $isEdit = $options['is_edit'];

        $builder
            ->add(
                'firstname',
                TextType::class,
                [
                    'label' => 'user_access.firstname'
                ]
            )
            ->add(
                'lastname',
                TextType::class,
                [
                    'label' => 'user_access.lastname'
                ]
            )
            ->add(
                'email',
                EmailType::class,
                [
                    'label' => 'user_access.email'
                ]
            )
            // ->add(
            //     'password',
            //     PasswordType::class,
            //     [
            //         'label' => 'Mot de passe : '
            //     ]
            // )
            ->add(
                'roles',
                ChoiceType::class,
                [
                    'label' => 'user_access.role',
                    'choices'  => [
                        'user.admin' => 'ROLE_ADMIN',
                        'user.moderator' => 'ROLE_MODERATOR',
                        'user.user' => 'ROLE_USER',
                    ],
                    'multiple' => true,
                    'expanded' => true
                ]
            );
            if (!$isEdit) {
        
                $builder->add(
                    'password',
                    PasswordType::class,
                    [
                        'label' => 'user_access.password',
                        'required' => !$isEdit, 
                    ]
                );
            }
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
            'is_edit' => false, 
        ]);
    }
}
