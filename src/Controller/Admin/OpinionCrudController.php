<?php

namespace App\Controller\Admin;

use App\Entity\Opinion;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Field\BooleanField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ChoiceField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use Doctrine\ORM\EntityManagerInterface;

class OpinionCrudController extends AbstractCrudController 
{

    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

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
            // ChoiceField::new('givenScore'),
            ChoiceField::new('givenScore')
                ->setChoices([
                    1 => '1',
                    2 => '2',
                    3 => '3',
                    4 => '4',
                    5 => '5',
                ]),
            //KEYWORDS
            BooleanField::new('reviewed'),
            BooleanField::new('accepted'),
            AssociationField::new('subject'),
            DateTimeField::new('creationDate')->setFormat('dd/MM/yyyy'),
            AssociationField::new('professor'),
            AssociationField::new('owner')
                // ->onlyOnDetail(),
                // ->setTemplatePath('admin/listsubjects.html.twig'),
        ];
    }

    


}
