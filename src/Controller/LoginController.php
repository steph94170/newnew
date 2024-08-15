<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class LoginController extends AbstractController
{

    #[Route('/connexion', name: 'app_login')]
    public function index(AuthenticationUtils $authenticationUtils): Response
    {
        
        // Gérer les erreurs de la dernière tentative de connexion, s'il y en a
        $error = $authenticationUtils->getLastAuthenticationError();
        
        // Récupérer le dernier nom d'utilisateur saisi par l'utilisateur
        $lastUsername = $authenticationUtils->getLastUsername();

        // Rendre la vue de la page de connexion en passant les erreurs et le dernier nom d'utilisateur
        return $this->render('login/index.html.twig', [
            'error' => $error,
            'last_username' => $lastUsername,
        ]);
    }

    #[Route('/deconnexion', name: 'app_logout', methods: ['GET'])]
    public function logout(): never
    {
        // Cette méthode ne sera jamais exécutée car la route de déconnexion est gérée par le système de sécurité de Symfony.
        // Le lanceur d'exception est utilisé ici pour indiquer clairement que cette méthode ne doit pas être atteinte.
        throw new \Exception("Error Processing Request");
    }
}
