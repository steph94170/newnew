<?php

namespace App\Controller\Admin;

use App\Entity\Carrier;
use App\Entity\Category;
use App\Entity\Contact;
use App\Entity\Header;
use App\Entity\Order;
use App\Entity\Product;
use App\Entity\User;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use EasyCorp\Bundle\EasyAdminBundle\Config\MenuItem;
use EasyCorp\Bundle\EasyAdminBundle\Config\Dashboard;
use EasyCorp\Bundle\EasyAdminBundle\Router\AdminUrlGenerator;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractDashboardController;

// Déclaration de la classe DashboardController qui hérite d'AbstractDashboardController d'EasyAdmin
class DashboardController extends AbstractDashboardController
{
    // Définition d'une route pour accéder au dashboard d'administration
    #[Route('/admin', name: 'admin')]
    public function index(): Response
    {
        // Génération de l'URL pour le contrôleur CRUD de l'entité User
        $adminUrlGenerator = $this->container->get(AdminUrlGenerator::class);
        return $this->redirect($adminUrlGenerator->setController(UserCrudController::class)->generateUrl());

        // Option 2. Vous pouvez faire en sorte que votre dashboard redirige vers différentes pages selon l'utilisateur
        //
        // if ('jane' === $this->getUser()->getUsername()) {
        //     return $this->redirect('...');
        // }

        // Option 3. Vous pouvez rendre un template personnalisé pour afficher un dashboard avec des widgets, etc.
        // (astuce : c'est plus facile si votre template étend @EasyAdmin/page/content.html.twig)
        //
        // return $this->render('some/path/my-dashboard.html.twig');
    }

    // Configuration du tableau de bord d'administration
    public function configureDashboard(): Dashboard
    {
        return Dashboard::new()
            ->setTitle('Chaussure-chic'); // Titre du dashboard
    }

    // Configuration des éléments de menu dans le tableau de bord d'administration
    public function configureMenuItems(): iterable
    {
        // Lien vers le tableau de bord principal
        yield MenuItem::linkToDashboard('Dashboard', 'fa fa-home');
        // Lien vers l'interface CRUD pour les utilisateurs
        yield MenuItem::linkToCrud('Utilisateur', 'fas fa-user', User::class);
        // Lien vers l'interface CRUD pour les catégories
        yield MenuItem::linkToCrud('Catégories', 'fas fa-folder-open', Category::class);
        // Lien vers l'interface CRUD pour les produits
        yield MenuItem::linkToCrud('Produits', 'fas fa-shoe-prints', Product::class);
        // Lien vers l'interface CRUD pour les transporteurs
        yield MenuItem::linkToCrud('Transporteurs', 'fas fa-truck-plane', Carrier::class);
        // Lien vers l'interface CRUD pour les commandes
        yield MenuItem::linkToCrud('Commandes', 'fas fa-cart-shopping', Order::class);
        // Lien vers l'interface CRUD pour les en-têtes
        yield MenuItem::linkToCrud('Header', 'fas fa-list', Header::class);
        // Lien vers l'interface CRUD pour les contacts
        yield MenuItem::linkToCrud('Contact', 'fas fa-address-book', Contact::class);
        // Lien vers le site public
        yield MenuItem::linkToUrl('retour au site', 'fa fa-store', '/');
    }
}
