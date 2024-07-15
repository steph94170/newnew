<?php

namespace App\Controller\Admin;

use App\Entity\Contact;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextareaField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextEditorField;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;

class ContactCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Contact::class;
    }
    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setEntityLabelInSingular('Contact')
            ->setEntityLabelInPlural('Contacts')
        ;
    }
    
    public function configureActions(Actions $actions): Actions
    {
        $show = Action::NEW('Affiche')->linkToCrudAction('show');

        return $actions
            ->add(Crud::PAGE_INDEX,$show)
            ->remove(Crud::PAGE_INDEX, Action::NEW)
            ->remove(Crud::PAGE_INDEX, Action::DELETE)
            ->remove(Crud::PAGE_INDEX, Action::EDIT);
           
        ;
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            TextField::new('firstname')->setlabel('Prénom'),
            TextField::new('lastname')->setlabel('Nom'),
            TextField::new('email')->setlabel('Email')->onlyOnIndex(),
            TextareaField::new('message'),

        ];
    }
}
