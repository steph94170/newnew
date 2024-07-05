<?php

namespace App\Controller;

use App\Class\Cart;
use App\Entity\Order;
use App\Form\OrderType;
use App\Entity\OrderDetail;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class OrderController extends AbstractController
{
    /*
        premiere etap du tunnel d'achat
        choix de l'adresse de livraion et du transporteur
    */
    #[Route('/commande/livraison', name: 'app_order')]
    public function index(): Response
    {

        $addresses = $this->getUser()->getAddresses();

        if (count($addresses) == 0 ) {
            return $this->redirectToRoute('app_account_address_form');
        }

        $form= $this->createForm(OrderType::class, null, [
            'addresses' => $addresses,
            'action' => $this->generateUrl('app_order_summary')
        ]);

        return $this->render('order/index.html.twig', [
            'deliveryForm' => $form->createView(),
        ]);
    }
        /*
        deuxieme etape du tunnel d'achat
        récap de la commande de l'utilisateur 
        Insertion en base de donnée
        préparation du payment vers stripe
    */
    #[Route('/commande/recapitulatif', name: 'app_order_summary')]
    public function add(Request $request, Cart $cart, EntityManagerInterface $entityManager): Response
    {
        if ($request->getMethod() != 'POST') {
            return $this->redirectToRoute('app_cart');
        }

        $products =  $cart->getCart();

        $form= $this->createForm(OrderType::class, null, [
            'addresses' => $this->getUser()->getAddresses(),
        ]);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid())
        {
           //stocker les informations base de donnée

           //creation de la chaine adresse
           $addressObj = $form->get('addresses')->getData();

           $address = $addressObj->getFirstName().' '.$addressObj->getLastName().'<br>';
           $address .= $addressObj->getAddress().'<br>';
           $address .= $addressObj->getPostal().' '.$addressObj->getCity().'<br>';
           $address .= $addressObj->getCountry().'<br>';
           $address .= $addressObj->getPhone(); 

           $order = new Order();
           $order->setCreatedAt(new \DateTime());
           $order->setState(1);
           $order->setCarrierName($form->get('carriers')->getData()->getName());
           $order->setCarrierPrice($form->get('carriers')->getData()->getPrice());
           $order->setDelivery($address);

           foreach ($products as $product) {
               $orderDetail = new OrderDetail();
               $orderDetail->setProductName($product['object']->getName());
               $orderDetail->setProductIllustration($product['object']->getIllustration());
               $orderDetail->setProductPrice($product['object']->getPrice());
               $orderDetail->setProductTva($product['object']->getTva());
               $orderDetail->setProductQuantity($product['qty']);
               $order->addOrderDetail($orderDetail);
           }

           
             // Insérons les données en base
             $entityManager->persist($order);
             //enregistre l'information
             $entityManager->flush();

        }

        return $this->render('order/summary.html.twig', [
            'choices' => $form->getData(),
            'cart' => $products,
            'totalWt' => $cart->getTotalWt(),
            'fullCartQuantity' => $cart->fullQuantity(),
        ]);
    }
}
