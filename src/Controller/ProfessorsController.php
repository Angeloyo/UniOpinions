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
use Symfony\Component\HttpFoundation\Request;

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

    #[Route('/u/{universitySlug}/{degreeSlug}/{subjectSlug}/{professorSlug}', name: 'app_professor')]
    public function showProfessor(
        string $universitySlug, 
        string $degreeSlug, 
        string $subjectSlug, 
        string $professorSlug,
        Request $request
        ): Response
    {

        $university = $this->universityRepository->findOneBySlug($universitySlug);
        $degree = $this->degreeRepository->findOneBySlugAndUniversity($degreeSlug, $university);
        $subject = $this->subjectRepository->findOneBySlugAndDegree($subjectSlug, $degree);
        $professor = $this->professorRepository->findOneBySlug($professorSlug);
        $acceptedOpinions = $this->opinionRepository->findAcceptedByProfessor($professor);

        $user = $this->getUser();
        $opinionExists = false;
        
        if ($user !== null) {
            $opinionExists = $this->opinionRepository->existsByUserAndProfessor($user, $professor);
        }

        $session = $request->getSession();
        // $referer = $request->headers->get('referer');
        $referer = $request->getUri();
        $session->set('referer', $referer);

        return $this->render('show_professor.html.twig', [
            'university' => $university,
            'degree' => $degree,
            'subject' => $subject,
            'professor' => $professor,
            'opinions' => $acceptedOpinions,
            'opinionExists' => $opinionExists,
        ]);
    }
}
