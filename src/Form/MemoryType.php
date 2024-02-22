<?php

namespace App\Form;

use App\Entity\User;
use App\Entity\Place;
use App\Entity\Memory;
use App\Entity\Location;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
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
                    'label' => 'memory.form.title'
                ]
            )
            ->add(
                'content',
                TextareaType::class,
                [
                    'label' => 'memory.form.content'
                ]
            )
            ->add(
                'main_picture',
                FileType::class,
                [
                    'label' => 'memory.form.main_picture',
                    'mapped' => false,
                    'required' => true,
                ]
            )
            ->add(
                'picture_date',
                DateType::class,
                [
                    'label' => 'memory.form.picture_date',
                    'placeholder' => ['année' => 'Année', 'mois' => 'Mois', 'jour' => 'Jour'],
                ]
            )
            // ->add('publishedAt')
            ->add('user', EntityType::class, 
            [
                'label' => 'memory.form.user',
                'class' => User::class,
                'choice_label' => function (User $user) {
                    return $user->getFirstname() . ' ' . $user->getLastname();
                },
            ])
            ->add('location', EntityType::class, 
            [
                'label' => 'memory.form.location',
                'class' => Location::class,
                'choice_label' => function (Location $location) {
                    return $location->getStreet() . ' ' . $location->getZipcode() . ' ' . $location->getCity();
                },
            ])
            ->add('place', EntityType::class, 
            [
                'label' => 'memory.form.place',
                'class' => Place::class,
                'choice_label' => function (Place $place) {
                    return $place->getName() . ' ' . $place->getType();
                },
            ])
                ->add('additionalPictures', FileType::class, 
            [
                    'label' => 'memory.form.picture',
                    'mapped' => false,
                    'required' => true,
                    'multiple' => true,   
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Memory::class,
        ]);
    }
}
