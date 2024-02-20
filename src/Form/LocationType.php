<?php

namespace App\Form;

use App\Entity\Location;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
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
                    'label' => 'location.form.area'
                ]
            )
            ->add(
                'department',
                TextType::class,
                [
                    'label' => 'location.form.department'
                ]
            )
            ->add(
                'district',
                TextType::class,
                [
                    'label' => 'location.form.district'
                ]
            )
            ->add(
                'street',
                TextType::class,
                [
                    'label' => 'location.form.street'
                ]
            )
            ->add(
                'city',
                TextType::class,
                [
                    'label' => 'location.form.city'
                ]
            )
            ->add(
                'zipcode',
                IntegerType::class,
                [
                    'label' => 'location.form.zipcode'
                ]
            )
            ->add(
                'latitude',
                NumberType::class,
                [
                    'label' => 'location.form.latitude'
                ]
            )
            ->add(
                'longitude',
                NumberType::class,
                [
                    'label' => 'location.form.longitude'
                ]
            );
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Location::class,
        ]);
    }
}
