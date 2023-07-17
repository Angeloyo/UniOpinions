<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Repository\UniversityRepository;
use App\Repository\DegreeRepository;
use App\Repository\SubjectRepository;

class SubjectsController extends AbstractController
{

    private $universityRepository;
    private $degreeRepository;
    private $subjectRepository;
    private $professorRepository;

    public function __construct(
        UniversityRepository $universityRepository,
        DegreeRepository $degreeRepository,
        SubjectRepository $subjectRepository,
        )
    {
        $this->universityRepository = $universityRepository;
        $this->degreeRepository = $degreeRepository;
        $this->subjectRepository = $subjectRepository;
    }

    #[Route('/{universitySlug}/{degreeSlug}/{subjectSlug}', name: 'app_subject')]
    public function showSubject(
        string $universitySlug, 
        string $degreeSlug, 
        string $subjectSlug,
        ): Response
    {

        $university = $this->universityRepository->findOneBySlug($universitySlug);
        $degree = $this->degreeRepository->findOneBySlugAndUniversity($degreeSlug, $university);
        $subject = $this->subjectRepository->findOneBySlugAndDegree($subjectSlug, $degree);
        $opinions = $subject->getOpinions();
        $professors = $subject->getProfessors();

        return $this->render('show_subject.html.twig', [
            'university' => $university,
            'degree' => $degree,
            'subject' => $subject,
            'opinions' => $opinions,
            'professors' => $professors,
        ]);
    }
}
