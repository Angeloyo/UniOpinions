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
use App\Repository\RelationSubjectProfessorRepository;
use Symfony\Component\HttpFoundation\Request;

class ProfessorsController extends AbstractController
{
    private $universityRepository;
    private $degreeRepository;
    private $subjectRepository;
    private $professorRepository;
    private $opinionRepository;
    private $relationsSubjectProfessorRepository;

    public function __construct(
        UniversityRepository $universityRepository,
        DegreeRepository $degreeRepository,
        SubjectRepository $subjectRepository,
        ProfessorRepository $professorRepository,
        OpinionRepository $opinionRepository,
        RelationSubjectProfessorRepository $relationSubjectProfessorRepository
        )
    {
        $this->universityRepository = $universityRepository;
        $this->degreeRepository = $degreeRepository;
        $this->subjectRepository = $subjectRepository;
        $this->professorRepository = $professorRepository;
        $this->opinionRepository = $opinionRepository;
        $this->relationsSubjectProfessorRepository = $relationSubjectProfessorRepository;
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
        
        $subject = $this->subjectRepository->findOneBySlugAndDegree($subjectSlug, $degree);
        
        if(!$subject){
            $this->addFlash('error', 'Asignatura no encontrada.');
            if ($referer) {
                return $this->redirect($referer);
            } else {
                return $this->redirectToRoute('app_home');
            }
        }

        if(!$subject->isAccepted()){
            $this->addFlash('error', 'Asignatura no aceptada.');
            if ($referer) {
                return $this->redirect($referer);
            } else {
                return $this->redirectToRoute('app_home');
            }
        }
        
        $professor = $this->professorRepository->findOneBySlug($professorSlug);
        
        if(!$professor){
            $this->addFlash('error', 'Profesor no encontrado.');
            if ($referer) {
                return $this->redirect($referer);
            } else {
                return $this->redirectToRoute('app_home');
            }
        }

        if(!$professor->isAccepted()){
            $this->addFlash('error', 'Profesor no aceptado.');
            if ($referer) {
                return $this->redirect($referer);
            } else {
                return $this->redirectToRoute('app_home');
            }
        }

        $relation_sp = $this->relationsSubjectProfessorRepository->findOneBy(['subject' => $subject, 'professor' => $professor]);
        
        // dump($relation_sp);
        // die();

        if(!$relation_sp){
            $this->addFlash('error', 'No encontrada relación del profesor con la asignatura');
            if ($referer) {
                return $this->redirect($referer);
            } else {
                return $this->redirectToRoute('app_home');
            }
        }

        // $acceptedOpinions = $this->opinionRepository->findAcceptedByProfessor($professor);
        $acceptedOpinions = $this->opinionRepository->findAcceptedBySubjectAndProfessor($professor, $subject);

        $user = $this->getUser();
        $opinionExists = false;
        
        if ($user !== null) {
            // $opinionExists = $this->opinionRepository->existsByUserAndProfessor($user, $professor);
            $opinionExists = $this->opinionRepository->existsByUserSubjectAndProfessor($user, $subject, $professor);
        }

        $session = $request->getSession();
        $referer = $request->getUri();
        $session->set('referer', $referer);

        return $this->render('show_professor.html.twig', [
            'university' => $university,
            'degree' => $degree,
            'subject' => $subject,
            'professor' => $professor,
            'opinions' => $acceptedOpinions,
            'opinionExists' => $opinionExists,
            'relation_sp' => $relation_sp
        ]);
    }
}
