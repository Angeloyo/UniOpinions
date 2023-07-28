<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Repository\UniversityRepository;
use App\Repository\DegreeRepository;
use App\Repository\SubjectRepository;
use Symfony\Component\HttpFoundation\Request;

class DegreesController extends AbstractController
{
    private $universityRepository;
    private $degreeRepository;
    private $subjectRepository;

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

    #[Route('/u/{universitySlug}/{degreeSlug}', name: 'app_degree')]
    public function showDegree(
        string $universitySlug, 
        string $degreeSlug,
        Request $request
        ): Response
    {

        $session = $request->getSession();
        // $referer = $request->headers->get('referer');
        $referer = $request->getUri();
        $session->set('referer', $referer);

        $university = $this->universityRepository->findOneBySlug($universitySlug);
        $degree = $this->degreeRepository->findOneBySlugAndUniversity($degreeSlug, $university);

        $subjects = $this->subjectRepository->findBy(['degree' => $degree, 'accepted' => true]);
        
        // Agrupa las asignaturas por año
        $subjectsByYear = [];
        foreach ($subjects as $subject) {
            $year = $subject->getYear();
            if (!isset($subjectsByYear[$year])) {
                $subjectsByYear[$year] = [];
            }
            $subjectsByYear[$year][] = $subject;
        }

        // Ordena las asignaturas por año en orden ascendente
        ksort($subjectsByYear);

        return $this->render('show_degree.html.twig', [
            'university' => $university,
            'degree' => $degree,
            // 'subjects' => $subjects,
            'subjects' => $subjectsByYear,
        ]);
    }
}
