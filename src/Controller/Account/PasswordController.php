<?php

namespace App\Controller\Account;

use App\Form\PasswordUserType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class PasswordController extends AbstractController
{
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
       $this->entityManager = $entityManager;
    }

    #[Route('/compte/modifier_mot_de_passe', name: 'app_account_modify_pwd')]
    public function index(Request $request,UserPasswordHasherInterface $passwordHasher): Response
    {
        $user =  $this->getUser();
       
        $form= $this->createForm(PasswordUserType::class, $user, [
            'passwordHasher' => $passwordHasher,
        ]);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid())
        {  
             //enregistre l'information
             $this->entityManager->flush();

             $this->addFlash('success', 'Votre mot de passe a été mis a jour.');

        }
        
        return $this->render('account/password/index.html.twig',[
            'modifyPwd' => $form->createView()
        ]);
    }
}