<?php

namespace App\Form;

use App\Entity\Picture;
use App\Entity\News;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;

class NewsType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('isHomeEvent', CheckboxType::class, [
                'label'    => 'Epingler à la page d\'accueil',
                'required' => false,
                'attr' => [
                    'class' => 'custom-checkbox',
                ],
            ])
            ->add('title', TextType::class, [
                'required' => false,
                'label' => 'Titre de la publication',
                'attr' => [
                    'class' => 'custom-title',
                ],
            ])
            ->add('content', TextareaType::class, [
                'required' => false,
                'label' => 'Texte ',                
                'attr' => [
                    'class' => 'custom-content',
                ],
            ])
            ->add('publishedAt', DateTimeType::class, [
                'label' => 'Date de publication',
                'widget' => 'single_text',
                'input' => 'datetime_immutable',
                'data' => new \DateTimeImmutable(),
                'attr' => [
                    'class' => 'custom-date',
                ],
            ])
            ->add('pictures', PictureType::class, [
                'data_class' => null
            ])       
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => News::class,
            'attr' => [
                'novalidate' => 'novalidate',
            ]
        ]);
    }
}
