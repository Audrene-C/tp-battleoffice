<?php

namespace App\Form;

use App\Entity\Shipping;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;

class ShippingType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
        ->add('clientFirstName')
        ->add('clientLastName')
        ->add('addressLine1')
        ->add('addressLine2', TextType::class, [
            'required' => false
        ])
        ->add('city')
        ->add('zipcode')
        ->add('country', ChoiceType::class, [
            'required' => false,
            'placeholder' => 'Choose a country',
            'choices' => [
                'France' => 'France',
                'Belgique' => 'Belgique',
                'Luxembourg' => 'Luxembourg'
                ]
            ])
        ->add('phone')
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Shipping::class,
        ]);
    }
}
