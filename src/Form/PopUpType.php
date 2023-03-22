<?php

namespace App\Form;

use App\Entity\PopUp;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class PopUpType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('title', TextType::class, [
                'label' => 'Titre de la pop-up',
                'attr' => [
                    'class' => 'custom-title',
                ],
            ])
            ->add('content', TextareaType::class, [
                'label' => 'Contenu de la pop-up',
                'attr' => [
                    'class' => 'custom-content',
            ]])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => PopUp::class,
            'attr' => [
                'novalidate' => 'novalidate',
            ]
        ]);
    }
}
