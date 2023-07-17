<?php

namespace App\Controller\Admin;

use App\Entity\Opinion;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Field\BooleanField;

class OpinionCrudController extends AbstractCrudController 
{
    public static function getEntityFqcn(): string
    {
        return Opinion::class;
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            IdField::new('id')->onlyOnDetail(),
            TextField::new('comment'),
            //SCORE
            //KEYWORDS
            BooleanField::new('reviewed'),
            BooleanField::new('accepted'),
            AssociationField::new('subject'),
            AssociationField::new('professor'),
            AssociationField::new('owner')
                // ->onlyOnDetail(),
                // ->setTemplatePath('admin/listsubjects.html.twig'),

        ];
    }
}
