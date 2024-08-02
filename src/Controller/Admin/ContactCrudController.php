<?php

namespace App\Controller\Admin;

use App\Entity\Contact;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextareaField;
use EasyCorp\Bundle\EasyAdminBundle\Context\AdminContext;
use EasyCorp\Bundle\EasyAdminBundle\Router\AdminUrlGenerator;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;

class ContactCrudController extends AbstractCrudController
{
    private $em;

    public function __construct(EntityManagerInterface $entityManagerInterface)
    {
        $this->em = $entityManagerInterface;
    }

    public static function getEntityFqcn(): string
    {
        return Contact::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setEntityLabelInSingular('Contact')
            ->setEntityLabelInPlural('Contacts');
    }

    public function configureActions(Actions $actions): Actions
    {
        $show = Action::NEW('Affiche')->linkToCrudAction('show');

        return $actions
            ->add(Crud::PAGE_INDEX, $show)
            ->remove(Crud::PAGE_INDEX, Action::NEW)
            ->remove(Crud::PAGE_INDEX, Action::DELETE)
            ->remove(Crud::PAGE_INDEX, Action::EDIT);
    }

    public function show(AdminContext $context, AdminUrlGenerator $adminUrlGenerator, Request $request)
    {
        $contact = $context->getEntity()->getInstance();
        // Récuperer l'url de notre action "show"
        $url = $adminUrlGenerator->setController(self::class)->setAction('show')->setEntityId($contact->getId())->generateUrl();

        return $this->render('admin/contact.html.twig', [
            'contact'=> $contact,
            'current_url' => $url
        ]);
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            IdField::new('id'),
            TextField::new('firstname')->setLabel('Prénom'),
            TextField::new('lastname')->setLabel('Nom'),
            TextField::new('email')->setLabel('Email')->onlyOnIndex(),
            TextareaField::new('message')->setLabel('Message')
        ];
    }
}
