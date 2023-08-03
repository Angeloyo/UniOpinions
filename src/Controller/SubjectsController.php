<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Repository\UniversityRepository;
use App\Repository\DegreeRepository;
use App\Repository\SubjectRepository;
use App\Repository\OpinionRepository;
use Symfony\Component\HttpFoundation\Request;

class SubjectsController extends AbstractController
{

    private $universityRepository;
    private $degreeRepository;
    private $subjectRepository;
    private $opinionRepository;

    public function __construct(
        UniversityRepository $universityRepository,
        DegreeRepository $degreeRepository,
        SubjectRepository $subjectRepository,
        OpinionRepository $opinionRepository
        )
    {
        $this->universityRepository = $universityRepository;
        $this->degreeRepository = $degreeRepository;
        $this->subjectRepository = $subjectRepository;
        $this->opinionRepository = $opinionRepository;
    }

    #[Route('/u/{universitySlug}/{degreeSlug}/{subjectSlug}', name: 'app_subject')]
    public function showSubject(
        string $universitySlug, 
        string $degreeSlug, 
        string $subjectSlug,
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

        $degree = $this->degreeRepository->findOneBySlugAndUniversity($degreeSlug, $university);
        
        if(!$degree){
            $this->addFlash('error', 'Grado no encontrado.');
            if ($referer) {
                return $this->redirect($referer);
            } else {
                return $this->redirectToRoute('app_home');
            }
        }
        
        $subject = $this->subjectRepository->findOneBySlugAndDegree($subjectSlug, $degree);
        
        if(!$subject){
            $this->addFlash('error', 'Asignatura no encontrada.');
            if ($referer) {
                return $this->redirect($referer);
            } else {
                return $this->redirectToRoute('app_home');
            }
        }
        
        // $acceptedOpinions = $this->opinionRepository->findAcceptedBySubject($subject);
        $acceptedOpinions = $this->opinionRepository->findAcceptedBySubjectAndNoProfessor($subject);

        $user = $this->getUser();
        $opinionExists = false;
        
        if ($user !== null) {
            // $opinionExists = $this->opinionRepository->existsByUserAndSubject($user, $subject);
            $opinionExists = $this->opinionRepository->existsByUserSubjectAndNoProfessor($user, $subject);
        }

        $acceptedProfessors = $subject->getAcceptedProfessors();

        $referer = $request->getUri();
        $session->set('referer', $referer);

        return $this->render('show_subject.html.twig', [
            'university' => $university,
            'degree' => $degree,
            'subject' => $subject,
            'opinions' => $acceptedOpinions,
            'professors' => $acceptedProfessors,
            'opinionExists' => $opinionExists,
        ]);
    }
}
