<?php

namespace App\Form;

use App\Entity\Place;
use App\Entity\Location;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;

class PlaceType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add(
                'name',
                TextType::class,
                [
                    'label' => 'place.name'
                ]
            )
            ->add(
                'type',
                TextType::class,
                [
                    'label' => 'place.type'
                ]
            )
            // ->add('createdAt')
            // ->add('updatedAt')
            ->add(
                'location',
                EntityType::class,
                [
                    'label' => 'place.address',
                    'class' =>
                    Location::class,
                    'choice_label' => function (Location $location) {
                        return $location->getStreet() . ' ' . $location->getZipcode() . ' ' . $location->getCity();
                    },
                ]
            );
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Place::class,
        ]);
    }
}
