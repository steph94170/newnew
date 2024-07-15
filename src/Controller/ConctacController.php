<?php

namespace App\Controller;

use App\Entity\Contact;
use App\Form\ConctactFormType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class ConctacController extends AbstractController
{
    #[Route('/formulaire-de-contact', name: 'app_conctac')]
    public function index(Request $request,EntityManagerInterface $em,): Response
    {
        $contact = new Contact();

        $contactForm = $this->createForm(ConctactFormType::class, $contact);

        $contactForm->handleRequest($request);

        if ( $contactForm->isSubmitted() && $contactForm->isValid() ) 
        {
            $em->persist($contact);
            $em->flush();

            $this->addFlash("success", "Votre message a bien été envoyé. Je vous répondrai dans les plus brefs délais.");

            return $this->redirectToRoute('app_conctac');
        }

        return $this->render('conctac/index.html.twig',[
            "contactForm" => $contactForm->createView()
        ]);
    }
}
