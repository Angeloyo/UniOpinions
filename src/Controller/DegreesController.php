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
        $referer = $session->get('referer');

        $university = $this->universityRepository->findOneBySlug($universitySlug);

        if(!$university){
            $this->addFlash('error', 'Universidad no encontrada.');
            if ($referer) {
                return $this->redirect($referer);
            } else {
                return $this->redirectToRoute('app_home');
            }
        }

        if(!$university->isAccepted()){
            $this->addFlash('error', 'Universidad no aceptada.');
            if ($referer) {
                return $this->redirect($referer);
            } else {
                return $this->redirectToRoute('app_home');
            }
        }

        $degree = $this->degreeRepository->findOneBySlugAndUniversity($degreeSlug, $university);

        if(!$degree){
            $this->addFlash('error', 'Grado no encontrado.');
            if ($referer) {
                return $this->redirect($referer);
            } else {
                return $this->redirectToRoute('app_home');
            }
        }

        if(!$degree->isAccepted()){
            $this->addFlash('error', 'Grado no aceptado.');
            if ($referer) {
                return $this->redirect($referer);
            } else {
                return $this->redirectToRoute('app_home');
            }
        }

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

        // Ordena las asignaturas alfabéticamente dentro de un mismo año
        foreach ($subjectsByYear as &$subjectsInYear) {
            usort($subjectsInYear, function ($a, $b) {
                //comparar sin tildes!
                return strcmp(iconv('UTF-8', 'ASCII//TRANSLIT', $a->getName()), iconv('UTF-8', 'ASCII//TRANSLIT', $b->getName()));
            });
        }

        $referer = $request->getUri();
        $session->set('referer', $referer);

        return $this->render('show_degree.html.twig', [
            'university' => $university,
            'degree' => $degree,
            'subjects' => $subjectsByYear,
        ]);
    }
}
