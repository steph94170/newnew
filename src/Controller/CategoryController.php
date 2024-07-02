<?php

namespace App\Controller;

use App\Repository\CategoryRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class CategoryController extends AbstractController
{
    #[Route('/categorie/{slug}', name: 'app_category')]
    public function index($slug, CategoryRepository $categoryRepository): Response
    {
        
        $category = $categoryRepository->findOneBySlug($slug);
        


        //1 jouvre une connexion avec la base de donner

        //2 connecte toi a la table qui s'appelle Category

        //3 fais une action en base de données


        return $this->render('category/index.html.twig', [
            'category' => $category,
        ]);
    }
}
