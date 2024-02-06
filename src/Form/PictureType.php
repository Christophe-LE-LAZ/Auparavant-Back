<?php

namespace App\Form;

use App\Entity\Memory;
use App\Entity\Picture;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\UrlType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

class PictureType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add(
                'picture',
                UrlType::class,
                [
                    'label' => 'Photo :'
                ]
            )
            ->add(
                'memory',
                EntityType::class,
                [
                    'class' =>
                    Memory::class,
                    'choice_label' => 'title',
                ]
            )
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
            'data_class' => Picture::class,
        ]);
    }
}
