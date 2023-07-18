<?php

namespace App\Controller\Admin;

use App\Entity\Opinion;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Field\BooleanField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IntegerField;
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
            IntegerField::new('givenScore'),
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

    public function persistEntity(EntityManagerInterface $entityManager, $entityInstance): void
    {
        if ($entityInstance instanceof Opinion) {
            $professor = $entityInstance->getProfessor();
            $subject = $entityInstance->getSubject();
            $score = $entityInstance->getGivenScore();

            if ($professor) {
                $professor->incrementScoreCount($score);
            }

            if ($subject) {
                $subject->incrementScoreCount($score);
            }
        }

        parent::persistEntity($entityManager, $entityInstance);
    }

    public function updateEntity(EntityManagerInterface $entityManager, $entityInstance): void
    {
        if ($entityInstance instanceof Opinion) {
            $professor = $entityInstance->getProfessor();
            $subject = $entityInstance->getSubject();
            $oldScore = $entityInstance->getOldScore();
            $newScore = $entityInstance->getGivenScore();

            if ($oldScore !== $newScore) {
                if ($professor) {
                    $professor->decrementScoreCount($oldScore);
                    $professor->incrementScoreCount($newScore);
                }

                if ($subject) {
                    $subject->decrementScoreCount($oldScore);
                    $subject->incrementScoreCount($newScore);
                }
            }
        }

        parent::updateEntity($entityManager, $entityInstance);
    }


}
