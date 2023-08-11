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
            IdField::new('id'),
            TextField::new('comment'),
            ChoiceField::new('givenScore')
                ->setChoices([
                    1 => '1',
                    2 => '2',
                    3 => '3',
                    4 => '4',
                    5 => '5',
                ]),
            ChoiceField::new('keywords')
            ->setChoices([
                'Lección estimulante' => 'leccion_estimulante',
                'Lección pesada' => 'leccion_pesada',
                'Disponible' => 'disponible',
                'Apreciado' => 'apreciado',
                'Trabajo en grupo' => 'trabajo_en_grupo',
                'Muchas tareas' => 'muchas_tareas',
                'Estricto' => 'estricto',
                'Corrección exigente' => 'correccion_exigente',
            ])
            ->allowMultipleChoices(),
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
