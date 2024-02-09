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
                    'label' => 'Prénom : '
                ]
            )
            ->add(
                'lastname',
                TextType::class,
                [
                    'label' => 'Nom : '
                ]
            )
            ->add(
                'email',
                EmailType::class,
                [
                    'label' => 'Adresse électronique : '
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
                    'label' => 'Rôle : ',
                    'choices'  => [
                        'Administrateur' => 'ROLE_ADMIN',
                        'Modérateur' => 'ROLE_MODERATOR',
                        'Utilisateur' => 'ROLE_USER',
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
                        'label' => 'Mot de passe : ',
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
