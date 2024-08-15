<?php

namespace App\Controller;

use App\Repository\HeaderRepository; // Importation du repository pour les en-têtes
use App\Repository\ProductRepository; // Importation du repository pour les produits
use Symfony\Component\HttpFoundation\Response; // Importation de la classe Response pour retourner la réponse HTTP
use Symfony\Component\Routing\Attribute\Route; // Importation de l'attribut Route pour définir les routes
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController; // Importation de la classe AbstractController qui fournit des fonctionnalités de base pour les contrôleurs

class HomeController extends AbstractController
{
    // Définition de la route pour la page d'accueil
    #[Route('/', name: 'app_home')]
    public function index(HeaderRepository $headerRepository, ProductRepository $productRepository): Response
    {
        // Récupération de tous les en-têtes depuis le repository
        $headers = $headerRepository->findAll();
        
        // Récupération des produits à afficher sur la page d'accueil
        $productsInHomePage = $productRepository->findByIsHomePage(true);

        // Rendu de la vue Twig 'home/index.html.twig' avec les données récupérées
        return $this->render('home/index.html.twig', [
            'headers' => $headers,
            'productsInHomePage' => $productsInHomePage
        ]);
    }
}

