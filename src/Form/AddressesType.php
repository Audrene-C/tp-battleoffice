<?php

namespace App\Form;

use App\Entity\Addresses;
use App\Entity\Billing;
use App\Entity\Shipping;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;

class AddressesType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('Billing', EntityType::class, [
                'class' => Billing::class
            ])
            ->add('Shipping', EntityType::class, [
                'class' => Shipping::class
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Addresses::class,
        ]);
    }
}
