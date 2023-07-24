<?php

namespace App\Controller\Admin;

use App\Entity\University;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Field\CollectionField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ArrayField;


class UniversityCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return University::class;
    }

    /*
    public function configureFields(string $pageName): iterable
    {
        return [
            IdField::new('id'),
            TextField::new('title'),
            TextEditorField::new('description'),
        ];
    }
    */

    public function configureFields(string $pageName): iterable
    {
        return [
            IdField::new('id')->onlyOnDetail(),
            // IdField::new('id'),
            TextField::new('name'),
            ArrayField::new('aliases'),
            TextField::new('slug')->onlyOnDetail(),
            CollectionField::new('degrees')
                ->onlyOnDetail()
                // ->setTemplatePath('custom_template.html.twig')
                ->setTemplatePath('admin/listdegrees.html.twig')
        ];
    }

}
