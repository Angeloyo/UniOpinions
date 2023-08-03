<?php

namespace App\Controller\Admin;

use App\Entity\Professor;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\CollectionField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ArrayField;
use EasyCorp\Bundle\EasyAdminBundle\Field\BooleanField;


class ProfessorCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Professor::class;
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            IdField::new('id')->onlyOnDetail(),
            BooleanField::new('accepted'),
            // BooleanField::new('reviewed'),
            TextField::new('name'),            
            TextField::new('slug')->onlyOnDetail(),
            ArrayField::new('scoreCount')->onlyOnDetail(),
            AssociationField::new('relationsSubjectProfessor')
                // ->onlyOnDetail(),
                ->setTemplatePath('admin/listsubjects.html.twig'),
            CollectionField::new('opinions')
                ->onlyOnDetail()
                //listar id de opiniones?
        ];
    }
}
