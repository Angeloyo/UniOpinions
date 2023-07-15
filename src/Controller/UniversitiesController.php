<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\University;
use App\Entity\Degree;
use App\Entity\Subject;

class UniversitiesController extends AbstractController
{

    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    #[Route('/universities', name: 'app_universities')]
    public function index(): Response
    {
        $universities = $this->entityManager->getRepository(University::class)->findAll();

        return $this->render('universities/index.html.twig', [
            'universities' => $universities,
        ]);
    }

    #[Route('/{universitySlug}', name: 'app_university')]
    public function show(string $universitySlug): Response
    {
        $university = $this->entityManager->getRepository(University::class)
            ->findOneBy(['slug' => $universitySlug]);

        $degrees = $this->entityManager->getRepository(Degree::class)
            ->findBy(['university' => $university]);

        if (!$university) {
            throw $this->createNotFoundException('La universidad especificada no existe');
        }

        return $this->render('universities/show.html.twig', [
            'university' => $university,
            'degrees' => $degrees,
        ]);
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

        return $this->render('universities/show_degree.html.twig', [
            'university' => $university,
            'degree' => $degree,
            // 'subjects' => $subjects,
            'subjects' => $subjectsByYear,
        ]);
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

        // Hacer lo que necesites con la asignatura...

        return $this->render('universities/show_subject.html.twig', [
            'university' => $university,
            'degree' => $degree,
            'subject' => $subject,
        ]);
    }


}