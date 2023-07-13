<?php

namespace App\Controller\Admin;

use App\Entity\Degree;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;


class DegreeCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Degree::class;
    }

    // public function configureFields(string $pageName): iterable
    // {
    //     yield IdField::new('id');
    //     yield TextField::new('name');
    //     yield AssociationField::new('university');
    // }

    // public function configureFields(string $pageName): iterable
    // {
    //     return [
    //         // IdField::new('id'),
    //         TextField::new('name'),
    //         AssociationField::new('university')
    //     ];
    // }

    public function configureFields(string $pageName): iterable
    {
        $fields = [
            IdField::new('id')->onlyOnDetail(),
            TextField::new('name'),
            AssociationField::new('university'),
        ];

        // if ($pageName === Crud::PAGE_DETAIL) {
        //     $fields = [IdField::new('id')] + $fields;
        // }

        return $fields;
    }

    
}
