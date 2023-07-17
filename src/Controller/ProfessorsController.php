<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Repository\UniversityRepository;
use App\Repository\DegreeRepository;
use App\Repository\SubjectRepository;
use App\Repository\ProfessorRepository;
use App\Repository\OpinionRepository;

class ProfessorsController extends AbstractController
{
    private $universityRepository;
    private $degreeRepository;
    private $subjectRepository;
    private $professorRepository;
    private $opinionRepository;

    public function __construct(
        UniversityRepository $universityRepository,
        DegreeRepository $degreeRepository,
        SubjectRepository $subjectRepository,
        ProfessorRepository $professorRepository,
        OpinionRepository $opinionRepository
        )
    {
        $this->universityRepository = $universityRepository;
        $this->degreeRepository = $degreeRepository;
        $this->subjectRepository = $subjectRepository;
        $this->professorRepository = $professorRepository;
        $this->opinionRepository = $opinionRepository;
    }

    #[Route('/{universitySlug}/{degreeSlug}/{subjectSlug}/{professorSlug}', name: 'app_professor')]
    public function showProfessor(
        string $universitySlug, 
        string $degreeSlug, 
        string $subjectSlug, 
        string $professorSlug,
        ): Response
    {

        $university = $this->universityRepository->findOneBySlug($universitySlug);
        $degree = $this->degreeRepository->findOneBySlugAndUniversity($degreeSlug, $university);
        $subject = $this->subjectRepository->findOneBySlugAndDegree($subjectSlug, $degree);
        $professor = $this->professorRepository->findOneBySlug($professorSlug);
        $acceptedOpinions = $this->opinionRepository->findAcceptedByProfessor($professor);

        return $this->render('show_professor.html.twig', [
            'university' => $university,
            'degree' => $degree,
            'subject' => $subject,
            'professor' => $professor,
            'opinions' => $acceptedOpinions,
        ]);
    }
}
