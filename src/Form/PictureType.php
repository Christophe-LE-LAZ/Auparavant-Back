<?php

namespace App\Form;

use App\Entity\Memory;
use App\Entity\Picture;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Validator\Constraints\File;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\FileType;

class PictureType extends AbstractType
{

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add(
                'picture',
                FileType::class,
                [
                    'label' => 'picture.picture',
                    'mapped' => false,
                    'required' => true,
                    'constraints' => [
                        new File([
                            'mimeTypes' => [
                                'image/*',
                            ],
                            'mimeTypesMessage' => 'Veuillez choisir un fichier valide',
                        ])
                        ],

                ]
            )
            ->add(
                'memory',
                EntityType::class,
                [
                    'label' => 'picture.memory',
                    'class' =>
                    Memory::class,
                    'choice_label' => 'title',
                ]
            );
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Picture::class,
        ]);
    }
}
