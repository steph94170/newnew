<?php

namespace App\Controller;

use Stripe\Stripe;
use App\Class\Cart;
use Stripe\Checkout\Session;
use App\Repository\OrderRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class PaymentController extends AbstractController
{
    #[Route('/commande/paiement/{id_order}', name: 'app_payment')]
    public function index($id_order, OrderRepository $orderRepository, EntityManagerInterface $entityManager): Response
    {

     //securité pour evité qu'un utilisateur aille chercher une commande qui ne lui appartiend pas en passent par la barre d'url
     $order = $orderRepository->findOneById([
      'id'=> $id_order,
      'user'=> $this->getUser()
     ]);

      // Si la commande n'existe pas ou n'appartient pas à l'utilisateur actuel, redirection vers la page d'accueil
     if (!$order) {
      return $this->redirectToRoute('app_home');
     }


     $product_for_stripe = [];

    // Parcourir les détails de la commande pour préparer les informations des produits pour Stripe
     foreach ($order->getOrderDetails() as $product) {

      // Définir la clé API Stripe
      Stripe::setApiKey($_ENV['STRIPE_SECRET_KEY']);

        // Préparer les informations du produit pour Stripe
        $product_for_stripe[] =[
            'price_data' => [
            'currency' => 'eur',
            'unit_amount'=> number_format($product->getProductPriceWt() * 100, decimals:0, decimal_separator:'', thousands_separator:'' ),
            'product_data'=>[
                'name' => $product->getProductName(),
                'images' => [
                  $_ENV['DOMAIN'].'/uploads/'.$product->getProductIllustration(),
                ]
            ]
          ],
          'quantity' => $product->getProductQuantity(),

        ];

     }

     // Ajouter les frais de transport comme un produit pour Stripe
     $product_for_stripe[] =[
      'price_data' => [
      'currency' => 'eur',
      'unit_amount'=> number_format($order->getCarrierPrice() * 100, decimals:0, decimal_separator:'', thousands_separator:'' ),
      'product_data'=>[
          'name' => 'Transporteur : '.$order->getCarrierName(),
      ]
    ],
    'quantity' => 1,

  ];
      // Créer une session de paiement Stripe
      $checkout_session = Session::create([
        'customer_email' => $this->getUser()->getEmail() ,
        'line_items' => [[
          $product_for_stripe,
        ]],
        'mode' => 'payment',
        'success_url' => $_ENV['DOMAIN'] . '/commande/merci/{CHECKOUT_SESSION_ID}',
        'cancel_url' => $_ENV['DOMAIN'] . '/mon-panier/annulation',
      ]);

      // Associer l'ID de la session Stripe à la commande et sauvegarder les modifications en base de données
      $order->setStripeSessionId($checkout_session->id);
      $entityManager->flush();
    
      // Rediriger l'utilisateur vers l'URL de la session de paiement Stripe
      return $this->redirect($checkout_session->url);
    }

    #[Route('/commande/merci/{stripe_session_id}', name: 'app_payment_success')]
    public function success($stripe_session_id, OrderRepository $orderRepository,EntityManagerInterface $entityManager, Cart $cart): Response
    {
      // Récupérer la commande correspondante à la session Stripe et à l'utilisateur actuel
      $order = $orderRepository->findOneBy([
        'stripe_session_id' => $stripe_session_id,
        'user' => $this->getUser()
      ]);

       // Si la commande n'existe pas ou n'appartient pas à l'utilisateur actuel, redirection vers la page d'accueil
      if (!$order) {
        return $this->redirectToRoute('app_home');
       }

    
      // Si l'état de la commande est 1 (indiquant que la commande a été validée), la passer à l'état 2 (paiement réussi)
       if ($order->getState() == 1) {
          $order->setState(state:2);
          // Vider le panier de l'utilisateur
          $cart->remove();
          // Sauvegarder les modifications en base de données
          $entityManager->flush();
       }
      

       //Rendre la vue de succès de paiement avec les informations de la commande
       return $this->render('payement/success.html.twig', [
        'order' => $order,
    ]);
    }
}
