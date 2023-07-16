<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\University;
use App\Entity\Degree;
use App\Entity\Subject;
use App\Entity\Professor;

class ProfessorsController extends AbstractController
{
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    #[Route('/{universitySlug}/{degreeSlug}/{subjectSlug}/{professorSlug}', name: 'app_professor')]
    public function showProfessor(string $universitySlug, string $degreeSlug, string $subjectSlug, string $professorSlug): Response
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

        $professor = $this->entityManager->getRepository(Professor::class)
            ->findOneBy(['slug' => $professorSlug]);

        if (!$professor) {
            throw $this->createNotFoundException('El profesor especificado no existe en la asignatura especificada');
        }

        $opinions = $professor->getOpinions();

        return $this->render('show_professor.html.twig', [
            'university' => $university,
            'degree' => $degree,
            'subject' => $subject,
            'professor' => $professor,
            'opinions' => $opinions,
        ]);
    }
}
