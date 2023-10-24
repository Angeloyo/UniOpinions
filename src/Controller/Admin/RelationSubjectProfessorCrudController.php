<?php

namespace App\Controller\Admin;

use App\Entity\RelationSubjectProfessor;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\BooleanField;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ArrayField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;

class RelationSubjectProfessorCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return RelationSubjectProfessor::class;
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            IdField::new('id')                
                ->hideWhenUpdating()
                ->hideWhenCreating()
                ,
            BooleanField::new('accepted')
                ->hideWhenUpdating()
                ,
            ArrayField::new('scoreCount')
                ->hideWhenCreating()
                ->hideWhenUpdating()
                ,
            TextField::new('keywordsCountDisplay', 'Keywords Count')
                ->hideWhenCreating()
                ->hideWhenUpdating()
                ,
            AssociationField::new('professor'),
            AssociationField::new('subject')
        ];
    }
}
