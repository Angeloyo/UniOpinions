<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\University;
use App\Entity\Degree;


class DegreesController extends AbstractController
{
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    #[Route('/{universitySlug}/{degreeSlug}', name: 'app_degree')]
    public function showDegree(string $universitySlug, string $degreeSlug): Response
    {
        $university = $this->entityManager->getRepository(University::class)
        ->findOneBy(['slug' => $universitySlug]);

        if (!$university) {
            throw $this->createNotFoundException('La Universidad especificada no existe');
        }

        $degree = $this->entityManager->getRepository(Degree::class)->findOneBy(['slug' => $degreeSlug, 'university' => $university]);
        if (!$degree) {
            throw $this->createNotFoundException('El Grado especificado no existe en la universidad especificada');
        }

        // Agrupa las asignaturas por año
        $subjects = $degree->getSubjects()->toArray();
        $subjectsByYear = [];
        foreach ($subjects as $subject) {
            $year = $subject->getYear();
            if (!isset($subjectsByYear[$year])) {
                $subjectsByYear[$year] = [];
            }
            $subjectsByYear[$year][] = $subject;
        }

        return $this->render('show_degree.html.twig', [
            'university' => $university,
            'degree' => $degree,
            // 'subjects' => $subjects,
            'subjects' => $subjectsByYear,
        ]);
    }
}
