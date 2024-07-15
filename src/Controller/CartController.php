<?php

namespace App\Controller;

use App\Class\Cart;
use App\Repository\ProductRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class CartController extends AbstractController
{
    #[Route('/mon-panier/{motif}', name: 'app_cart', defaults:['motif' => null])]
    public function index(Cart $cart, $motif): Response
    {
        if ($motif == "annulation") {
            $this->addFlash(
                type: 'info',
                message: ' Paiment annulé vous pouvez mettre à jour votre panier et votre commande.'
            );
        }
        return $this->render('cart/index.html.twig',[
            'cart' => $cart->getCart(),
            'totalWt' => $cart->getTotalWt()
        ]);
    }
    #[Route('/cart/add/{id}', name: 'app_cart_add')]
    public function add($id , Cart $cart, ProductRepository $productRepository, Request $request): Response
    {
        $product = $productRepository->findOneById($id);
        $size = $request->request->get('size');

        if ($size) {
            $cart->add($product, $size);
            $this->addFlash('success', 'Produit ajouté à votre panier.');
        } else {
            $this->addFlash('error', 'Veuillez sélectionner une taille.');
        }

        return $this->redirect($request->headers->get('referer'));
    }

    #[Route('/cart/remove', name: 'app_cart_remove')]
    public function remove(Cart $cart, ): Response
    {

        $cart->remove();
     
        return $this->redirectToRoute('app_home');
    }


    #[Route('/cart/decrease/{id}', name: 'app_cart_decrease')]
    public function decrease($id , Cart $cart): Response
    {
        
        $cart->decrease($id);
        
        $this->addFlash('success', 'Produit supprimé de votre panier.');

        return $this->redirectToRoute('app_cart');
    }
}
