<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\FormError;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;

class PasswordUserType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
        ->add('actualPassword', PasswordType::class, [
            'mapped' => false,
            'label' => 'Mot de passe actuel'
        ])
        ->add('plainPassword', RepeatedType::class, [
            'type' => PasswordType::class,
            'mapped' => false,
            'invalid_message' => 'Les mots de passe doivent correspondre.',
            'first_options'  => ['label' => 'Nouveau mot de passe', 'hash_property_path' => 'password'],
            'second_options' => ['label' => 'Confirmez le nouveau mot de passe'],
            'constraints' => [
                new Length([
                    'min' => 12,
                    'minMessage' => 'Le mot de passe doit comporter au moins {{ limit }} caractères.'
                ])
            ],
        ])

        ->addEventListener(FormEvents::SUBMIT, function(FormEvent $event){
             $form = $event->getForm();
             $user = $form->getconfig()->getOptions()['data'];
             $passwordHasher = $form->getConfig()->getOptions()['passwordHasher'];

            //1 recuperer le mot de passe saisi par l'utilisateur et le comparer au mdp bdd dans l'entité
            // $actualPwd = $form->get("actualPassword")->getData();

            $isValid = $passwordHasher->isPasswordValid(
                $user,
                $form->get("actualPassword")->getData()
            );

             //3 si c'est different renvoyer une erreur
            
            if(!$isValid){
                $form->get('actualPassword')->addError(new FormError("Votre message actuel n'est pas conforme."));
            }
           
        });

    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
            'passwordHasher' => null
        ]);
    }
}
