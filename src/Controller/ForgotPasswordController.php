<?php
namespace App\Controller;

use DateTime;
use App\Class\Mail;
use App\Repository\UserRepository;
use App\Form\ResetPasswordFormType;
use App\Form\ForgotPasswordFormType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class ForgotPasswordController extends AbstractController
{
    private $em;

    // Constructeur pour injecter l'EntityManager
    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    // Route pour la page de demande de réinitialisation de mot de passe
    #[Route('/mot-de-passe-oublie', name: 'app_password')]
    public function index(Request $request, UserRepository $userRepository): Response
    {
        // 1. Création du formulaire de demande de réinitialisation de mot de passe
        $form = $this->createForm(ForgotPasswordFormType::class);
        $form->handleRequest($request);
  
        // 2. Traitement du formulaire après soumission
        if ($form->isSubmitted() && $form->isValid())
        {
            // 3. Vérification si l'email renseigné existe en base de données
            $email = $form->get('email')->getData();
            $user = $userRepository->findOneByEmail($email);
          
            // 4. Notification à l'utilisateur que le mail de réinitialisation sera envoyé si l'email existe
            $this->addFlash('success', "Si votre adresse email existe, vous recevrez un mail pour réinitialiser votre mot de passe.");
            if ($user) {
                // 5. Création d'un token pour la réinitialisation du mot de passe et stockage en BDD
                $token = bin2hex(random_bytes(15)); // Génération d'un token aléatoire
                $user->setToken($token);

                $date = new DateTime();
                $date->modify('+10 minutes'); // Expiration du token dans 10 minutes
                $user->setTokenExpireAt($date);

                $this->em->flush(); // Enregistrement des modifications en BDD

                // Envoi de l'email avec le lien de réinitialisation
                $mail = new Mail();
                $vars = [
                    'link' =>  $this->generateUrl('app_password_update', ['token' => $token], UrlGeneratorInterface::ABSOLUTE_URL),
                ];
                $mail->send($user->getEmail(), $user->getFirstName().' '.$user->getLastName(), "Modification de votre mot de passe", 
                "forgotpassword.html", $vars);
            }
        }

        // Affichage du formulaire de demande de réinitialisation
        return $this->render('password/index.html.twig', [
            'forgotPasswordForm' => $form->createView(),
        ]);
    }

    // Route pour la page de réinitialisation du mot de passe
    #[Route('/mot-de-passe/reset/{token}', name: 'app_password_update')]
    public function update(Request $request, UserRepository $userRepository, $token): Response
    {
        // Redirection si le token est manquant
        if (!$token) {
            return $this->redirectToRoute('app_password');
        }

        // Recherche de l'utilisateur avec le token donné
        $user = $userRepository->findOneByToken($token);
        $now = new DateTime();

        // Redirection si le token est invalide ou expiré
        if (!$user || $now > $user->getTokenExpireAt()) {
            return $this->redirectToRoute('app_password');
        }
        
        // Création du formulaire de réinitialisation de mot de passe
        $form = $this->createForm(ResetPasswordFormType::class, $user);
        $form->handleRequest($request);

        // Traitement du formulaire après soumission
        if ($form->isSubmitted() && $form->isValid()) {
            // Réinitialisation du token et de sa date d'expiration
            $user->setToken(null);
            $user->setTokenExpireAt(null);
            
            $this->em->flush(); // Enregistrement des modifications en BDD
            $this->addFlash(
                'success',
                "Votre mot de passe est correctement mis à jour."
            );
            // Redirection vers la page de connexion
            return $this->redirectToRoute('app_login');
        }

        // Affichage du formulaire de réinitialisation de mot de passe
        return $this->render('password/reset.html.twig', [
            'form' => $form->createView()
        ]);
    }
}
