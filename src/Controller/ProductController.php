<?php

namespace App\Controller;

use App\Repository\ProductRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class ProductController extends AbstractController
{
    // Définition de la route pour accéder à un produit spécifique par son slug
    #[Route('/produit/{slug}', name: 'app_product')]
    public function index($slug, ProductRepository $productRepository): Response
    {
        // Récupération du produit correspondant au slug fourni
        $product = $productRepository->findOneBySlug($slug);

        // Si le produit n'existe pas, redirection vers la page d'accueil
        if (!$product) {
            return $this->redirectToRoute('app_home');
        }

        // Rendu de la vue 'product/index.html.twig' avec les données du produit
        return $this->render('product/index.html.twig', [
            'product' => $product,
        ]);
    }
}
