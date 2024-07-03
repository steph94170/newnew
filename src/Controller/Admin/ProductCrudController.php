<?php

namespace App\Controller\Admin;

use App\Entity\Product;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\SlugField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Field\NumberField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextEditorField;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ChoiceField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ImageField;

class ProductCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Product::class;
    }
    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setEntityLabelInSingular('Produit')
            ->setEntityLabelInPlural('Produits')
        ;
    }

    public function configureFields(string $pageName): iterable
    {
        $required = true;

        if($pageName == 'edit')
        {
            $required = false;
        }
        return [
        
            TextField::new('name')->setLabel('Nom')->setHelp('Nom de votre produit'),
            NumberField::new('size')->setLabel('Taille')->setHelp('La taille de votre produit en unités numériques'),
            SlugField::new('slug')->setLabel('URL')->setTargetFieldName('name')->setHelp('URL de votre catégorie générée automatiquement'),
            NumberField::new('price')->setLabel('Prix')->setHelp('Le prix H.T du produit sans le sigle €'),
            ChoiceField::new('tva')->setLabel('Taux de tva')->setChoices(['5,5%'=>'5,5', '20%'=>'20']),
            ImageField::new('illustration')->setLabel('Image')->setHelp("L'image du produit en 600x600px")->setUploadedFileNamePattern('[year]-[month]-[day]-[contenthash].[extension]')->setBasePath('/uploads')->setUploadDir('/public/uploads')->setRequired($required),
            TextEditorField::new('description')->setLabel('Description')->setHelp('Description de votre produit'),
            AssociationField::new('category')->setLabel('Categorie associée')
        ];
    }
    
    
}
