<?php

namespace App\Controller\Account;

use App\Entity\Address;
use App\Form\AddressUserType;
use App\Repository\AddressRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class AddressController extends AbstractController
{
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
       $this->entityManager = $entityManager;
    }
    
    #[Route('/compte/addresses', name: 'app_account_addresses')]
    public function index(): Response
    {
        return $this->render('account/address/index.html.twig');
    }

    #[Route('/compte/address/delete/{id}', name: 'app_account_address_delete')]
    public function delete($id, AddressRepository $addressRepository): Response
    {
        $address = $addressRepository->findOneById($id);
        if (!$address OR  $address->getUser() != $this->getUser()) {
            return $this->redirectToRoute('app_account_addresses');
        }
        $this->addFlash('success', 'Votre adresse est correctement supprimée.');

        $this->entityManager->remove($address);
        $this->entityManager->flush();

        return $this->redirectToRoute('app_account_addresses');
    }


    #[Route('/compte/adresse/ajouter{id}', name: 'app_account_address_form', defaults:['id'=>null])]
    public function Form(Request $request, $id, AddressRepository $addressRepository): Response
    {
        if ($id) {
            $address = $addressRepository->findOneById($id);
            //condition pour sécuriser l'adresse en verifiant si l'adresse appartient bien a l'utilisateur connécté 
            if (!$address OR  $address->getUser() != $this->getUser()) {
                return $this->redirectToRoute('app_account_addresses');
            }
        }else{
        $address = new Address();
        $address->setUser($this->getUser());
        }

        $form = $this->createForm(AddressUserType::class, $address);

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid())
        {
            
            $this->entityManager->persist($address);
    
            $this->entityManager->flush();

            $this->addFlash('success', 'Votre adresse est correctement sauvegardée.');

            return $this->redirectToRoute('app_account_addresses');

        }

        return $this->render('account/address/form.html.twig', [
            'addressForm' => $form
        ]);
    }
}