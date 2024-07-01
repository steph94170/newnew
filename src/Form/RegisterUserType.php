<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Validator\Constraints\Length;

class RegisterUserType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('firstName', TextType::class)
            ->add('lastName', TextType::class)
            ->add('email', EmailType::class)
            ->add('plainPassword', RepeatedType::class, [
                'constraints' =>[
                    new Length([
                        'min' => 12
                    ])
                ],
                'type' => PasswordType::class,
                'first_options'  => ['label' => 'Votre mot de passe', 'hash_property_path' => 'password'],
                'second_options' => ['label' => 'confirmez votre mot de pass'],
                'mapped' => false,
            ]);
            
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'constraints' =>[
                    new UniqueEntity([
                        'entityClass' => User::class,
                        'fields' => 'email',
                    ])
                ],
            'data_class' => User::class,
        ]);
    }
}
