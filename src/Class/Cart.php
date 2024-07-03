<?php
namespace App\Class;

use App\Entity\Product;
use Symfony\Component\HttpFoundation\RequestStack;

class Cart 
{
    //injection de la depense qui va me permettre d'aller chercher ma session
    public function __construct(private RequestStack $requestStack)
    {
        
    }
    public function add($product)
    {
        //appeler la session
        $cart = $this->requestStack->getSession()->get('cart');

        //ajouter une quantité +1 a  mon produit
        if(isset($cart [$product->getId()])){
             $cart[$product->getId()] = [
            'object'=> $product,
            'qty'=> $cart[$product->getId()]['qty'] + 1
        ];

        }else{
            $cart[$product->getId()] = [
                'object'=> $product,
                'qty'=>  1
            ];    

        }
      
        //créer ma session cart
        $this->requestStack->getSession()->set('cart',$cart);

        

    }

     //methode pour enlever des produits 
     public function decrease($id)
     {
        $cart = $this->requestStack->getSession()->get('cart');
        if($cart[$id]['qty']>1)
        {
            $cart[$id]['qty'] =  $cart[$id]['qty'] - 1;
        }else{
            unset($cart[$id]);
        }

        $this->requestStack->getSession()->set('cart',$cart);
     }
    
     
     public function getTotalWt()
     {
        $cart = $this->requestStack->getSession()->get('cart');
        $price = 0;

        if (!isset($cart)) {
            return $price;
        }

        foreach ($cart as $product) {
           
            $price = $price + ($product['object']->getPriceWt()* $product['qty']);
           
        }

            return $price;
     }


    //methode pour vider le panier
    public function remove()
    {
        return $this->requestStack->getSession()->remove('cart');
    }

    public function fullQuantity()
    {
        $cart = $this->requestStack->getSession()->get('cart');
        $quantity = 0;

        if (!isset($cart)) {
            return $quantity;
        }


        foreach ($cart as $product) {
           
            $quantity = $quantity + $product['qty'];

        }

        return $quantity;
    }


    public function getCart()
    {
        return  $this->requestStack->getSession()->get('cart');
    }
}