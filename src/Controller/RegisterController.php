<?php

namespace App\Controller;

use App\Class\Mail;
use App\Entity\User;
use App\Form\RegisterUserType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class RegisterController extends AbstractController
{
    #[Route('/inscription', name: 'app_register')]
    public function index(Request $request,EntityManagerInterface $entityManager): Response
    {
        $user = new User();
        
        $form= $this->createForm(RegisterUserType::class, $user);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid())
        {
           
            // Insérons les données en base
            $entityManager->persist($user);
            //enregistre l'information
            $entityManager->flush();

            $this->addFlash('success', 'Votre compte est correctement créé, veuillez vous connecter.');

            //Envoie d'un email de confirmation d'inscription
            $mail = new Mail();
            $vars = [
                'firstname' => $user->getFirstName(),
            ];
            $mail->send($user->getEmail(), $user->getFirstName().' '.$user->getLastName(), "Bienvenue sur La Boutique Chaussure-chic", "welcome.html", $vars);

            return $this->redirectToRoute('app_login');
        }

        return $this->render('register/index.html.twig',[

            'registrationForm'=> $form->createView()  
        ]);
    }
}
