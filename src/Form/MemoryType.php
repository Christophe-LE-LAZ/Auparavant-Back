<?php

namespace App\Form;

use App\Entity\User;
use App\Entity\Memory;
use App\Entity\Location;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\UrlType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;

class MemoryType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add(
                'title',
                TextType::class,
                [
                    'label' => 'Titre :'
                ]
            )
            ->add(
                'content',
                TextareaType::class,
                [
                    'label' => 'Contenu :'
                ]
            )
            ->add(
                'picture_date',
                DateType::class,
                [
                    'placeholder' => ['année' => 'Année', 'mois' => 'Mois', 'jour' => 'Jour'],
                ]
            )
            ->add('main_picture',
            UrlType::class,
                [
                    'label' => 'Photo principale :'
                ]
            )
            // ->add('publishedAt')
            ->add('user', EntityType::class, [
                'class' => User::class,
                'choice_label' => 'id',
            ])
            ->add('location', EntityType::class, [
                'class' => Location::class,
                'choice_label' => 'id',
            ])
            ->add(
                'submit',
                SubmitType::class,
                [
                    'label' => 'Valider'
                ]
            );
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Memory::class,
        ]);
    }
}
