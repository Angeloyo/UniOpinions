<?php

namespace App\Controller\Admin;

use App\Entity\Subject;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Field\CollectionField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IntegerField;
use EasyCorp\Bundle\EasyAdminBundle\Field\BooleanField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ArrayField;

class SubjectCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Subject::class;
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            IdField::new('id')
                ->hideWhenCreating()
                ->hideWhenUpdating()
                ,
            BooleanField::new('accepted')
                ->hideWhenUpdating()
                ,
            // BooleanField::new('reviewed'),
            // IdField::new('id'),
            TextField::new('name'),
            TextField::new('slug')->onlyOnDetail(),
            ArrayField::new('scoreCount')
                ->hideWhenUpdating()
                ,
            TextField::new('keywordsCountDisplay', 'Keywords Count')
                ->hideWhenUpdating()
                ->setFormTypeOption('mapped', false)
                ,
            AssociationField::new('degree'),
            IntegerField::new('year'),
            CollectionField::new('relationsSubjectProfessor')
                // ->onlyOnDetail(),
                ->hideWhenUpdating()
                ->setFormTypeOption('mapped', false)
                ->setTemplatePath('admin/listprofessors.html.twig')
                ,
            CollectionField::new('opinions')
                ->onlyOnDetail()
        ];
    }
}
