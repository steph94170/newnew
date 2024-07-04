<?php

namespace App\Form;

use App\Entity\User;
use App\Entity\Address;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\CountryType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;

class AddressUserType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('firstName', TextType::class, [
                'label' => 'Votre prénom',
                'attr' => ['placeholder'=>'Indiquez votre prénom']
            ])
            ->add('lastName' , TextType::class, [
                'label' => 'Votre Nom',
                'attr' => ['placeholder'=>'Indiquez votre nom']
            ])
            ->add('address', TextType::class, [
                'label' => 'Votre adresse',
                'attr' => ['placeholder'=>'Indiquez votre adresse']
            ])
            ->add('postal', TextType::class, [
                'label' => 'Votre code postal',
                'attr' => ['placeholder'=>'Indiquez votre code postal']
            ])
            ->add('city', TextType::class, [
                'label' => 'Votre ville',
                'attr' => ['placeholder'=>'Indiquez votre ville']
            ])
            ->add('country', CountryType::class,  [
                'label' => 'Votre pays',
                'attr' => ['placeholder'=>'Indiquez votre pays']
            ])
            ->add('phone', TextType::class, [
                'label' => 'Votre téléphone',
                'attr' => ['placeholder'=>'Indiquez votre nouméro de téléphone']
            ])
            ->add('submit', SubmitType::class, [
                'label' => 'Sauvegarder',
                'attr' => ['class'=>'btn btn-success']
            ])
        
        
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Address::class,
        ]);
    }
}
