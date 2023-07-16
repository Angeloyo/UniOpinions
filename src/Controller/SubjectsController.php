<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\University;
use App\Entity\Degree;
use App\Entity\Subject;


class SubjectsController extends AbstractController
{
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    #[Route('/{universitySlug}/{degreeSlug}/{subjectSlug}', name: 'app_subject')]
    public function showSubject(string $universitySlug, string $degreeSlug, string $subjectSlug): Response
    {
        $university = $this->entityManager->getRepository(University::class)
            ->findOneBy(['slug' => $universitySlug]);

        if (!$university) {
            throw $this->createNotFoundException('La Universidad especificada no existe');
        }

        $degree = $this->entityManager->getRepository(Degree::class)
            ->findOneBy(['slug' => $degreeSlug, 'university' => $university]);

        if (!$degree) {
            throw $this->createNotFoundException('El Grado especificado no existe en la universidad especificada');
        }

        $subject = $this->entityManager->getRepository(Subject::class)
            ->findOneBy(['slug' => $subjectSlug, 'degree' => $degree]);

        if (!$subject) {
            throw $this->createNotFoundException('La asignatura especificada no existe en el grado especificado');
        }

        $opinions = $subject->getOpinions();
        $professors = $subject->getProfessors();

        // Hacer lo que necesites con la asignatura...

        return $this->render('universities/show_subject.html.twig', [
            'university' => $university,
            'degree' => $degree,
            'subject' => $subject,
            'opinions' => $opinions,
            'professors' => $professors,
        ]);
    }
}
