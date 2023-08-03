<?php

namespace App\Controller\Admin;

use App\Entity\RelationSubjectProfessor;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\BooleanField;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;

class RelationSubjectProfessorCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return RelationSubjectProfessor::class;
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            IdField::new('id')->onlyOnDetail(),
            BooleanField::new('accepted'),
            AssociationField::new('professor'),
            AssociationField::new('subject')
        ];
    }
}
