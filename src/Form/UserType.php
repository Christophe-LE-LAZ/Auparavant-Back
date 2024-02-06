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
                    'label' => 'Nom de famille : '
                ]
            )
            ->add(
                'email',
                EmailType::class,
                [
                    'label' => 'Adresse électronique : '
                ]
            )
            ->add(
                'password',
                PasswordType::class,
                [
                    'label' => 'Mot de passe : '
                ]
            )
            ->add(
                'role',
                ChoiceType::class,
                [
                    'label' => 'Rôle : ',
                    'choices'  => [
                        'Administrateur' => 'ROLE_ADMIN',
                        'Responsable' => 'ROLE_MANAGER',
                        'Utilisateur' => 'ROLE_USER',
                    ],
                    'multiple' => true,
                    'expanded' => true
                ]
            );
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
