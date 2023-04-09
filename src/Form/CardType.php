<?php

namespace App\Form;

use App\Entity\Card;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;

class CardType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder         
            
        ->add('email', TextType::class, [
                
            'label' => 'Votre adresse mail ( Vous recevrez la carte cadeau en pdf ):',
            'attr' => [
                'class' => 'custom-title',
            ],
        ])

            ->add('gifter', TextType::class, [
                
                'label' => 'De la part de :',
                'attr' => [
                    'class' => 'custom-title',
                ],
            ])
            ->add('receiver', TextType::class, [
                
                'label' => 'A l\'intention de :',
                'attr' => [
                    'class' => 'custom-title',
                ],
            ])
            ->add('amount', MoneyType::class, [
                'label' => 'Montant',
            ])
            // ->add('boughtAt', DateTimeType::class, [
            //     'label' => 'Date d\'achat de la carte cadeau',
            //     'widget' => 'single_text',
            //     'input' => 'datetime_immutable',
            //     'data' => new \DateTimeImmutable(),
            //     'attr' => [
            //         'class' => 'custom-date',]                
            // ]);
            ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Card::class,
        ]);
    }
}
