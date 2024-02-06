<?php

namespace App\Form;

use App\Entity\Location;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;

class LocationType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add(
                'area',
                TextType::class,
                [
                    'label' => 'Région :'
                ]
            )
            ->add(
                'department',
                TextType::class,
                [
                    'label' => 'Département :'
                ]
            )
            ->add(
                'district',
                TextType::class,
                [
                    'label' => 'Quartier:'
                ]
            )
            ->add(
                'street',
                TextType::class,
                [
                    'label' => 'Rue :'
                ]
            )
            ->add(
                'city',
                TextType::class,
                [
                    'label' => 'Ville :'
                ]
            )
            ->add(
                'zipcode',
                IntegerType::class,
                [
                    'label' => 'Code postal :'
                ]
            )
            ->add('latitude')
            ->add('longitude');
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Location::class,
        ]);
    }
}
