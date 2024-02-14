<?php

namespace App\Form;

use App\Entity\Memory;
use App\Entity\Picture;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\UrlType;
use Symfony\Component\Form\Extension\Core\Type\FileType;

class PictureType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
        ->add('picture', FileType::class, [
            'label' => 'Photo :',
            'mapped' => false,
            'required' => true,
            
    ])
            ->add(
                'memory',
                EntityType::class,
                [
                    'label' => 'Souvenir : ',
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
