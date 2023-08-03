<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use App\Entity\Opinion;
use Doctrine\ORM\EntityManagerInterface;
use App\Repository\ProfessorRepository;
use App\Repository\SubjectRepository;

class CreateSpecificOpinionController extends AbstractController
{

    private $professorRepository;
    private $subjectRepository;
    private $entityManager;
    public function __construct(
        ProfessorRepository $professorRepository,
        SubjectRepository $subjectRepository,
        EntityManagerInterface $entityManager
        )
    {
        $this->professorRepository = $professorRepository;
        $this->subjectRepository = $subjectRepository;
        $this->entityManager = $entityManager;
    }

    #[Route('/opinion/new-specific/{subjectId}/{professorId?}', name: 'app_create_specific_opinion')]
    public function createSpecificOpinion(
        // string $type, 
        int $subjectId,
        ?int $professorId = null,
        Request $request,
        ): Response
    {
        $this->denyAccessUnlessGranted('ROLE_USER');

        $user = $this->getUser();
        $session = $request->getSession();
        $referer = $session->get('referer');

        if (!$user->isVerified()) {
            if ($referer) {
                return $this->redirect($referer);
            } else {
                return $this->redirectToRoute('app_home');
            }
        }
        
        $opinion = new Opinion();
        $userId = $this->getUser()->getId();

        if ($professorId !== null) {

            $professor = $this->professorRepository->find($professorId);
            if (!$professor) {
                // throw $this->createNotFoundException('Profesor no encontrado');
                $this->addFlash('error', 'Profesor no encontrado.');
                if ($referer) {
                    return $this->redirect($referer);
                } else {
                    return $this->redirectToRoute('app_home');
                }
            }

            $subject = $this->subjectRepository->find($subjectId);
            if (!$subject) {
                // throw $this->createNotFoundException('Profesor no encontrado');
                $this->addFlash('error', 'Asignatura no encontrada.');
                if ($referer) {
                    return $this->redirect($referer);
                } else {
                    return $this->redirectToRoute('app_home');
                }
            }

            //comprobar que el profesor esta relacionado con la asignatura
            if(!$professor->getRelationsSubjectProfessor()->exists(function($key, $relation) use ($subject) {
                return $relation->getSubject() === $subject;
            })) {
                $this->addFlash('error', 'El profesor no está relacionado con esa asignatura.');
                if ($referer) {
                    return $this->redirect($referer);
                } else {
                    return $this->redirectToRoute('app_home');
                }
            }
            
            // $existingOpinion = $professor->getOpinions()->filter(function($opinion) use ($userId) {
            //     return $opinion->getOwner()->getId() == $userId;
            // })->first();

            $existingOpinion = $professor->getOpinions()->filter(function($opinion) use ($userId, $subject) {
                return $opinion->getOwner()->getId() == $userId && $opinion->getSubject() === $subject;
            })->first();

            if ($existingOpinion) {
                return $this->redirectToRoute('app_edit_opinion', [
                    'id' => $existingOpinion->getId()
                ]);
            }

            $opinion->setProfessor($professor);
            $opinion->setSubject($subject);

            $object = $professor;

        } else{

            $subject = $this->subjectRepository->find($subjectId);
            if (!$subject) {
                // throw $this->createNotFoundException('Subject not found');
                $this->addFlash('error', 'Asignatura no encontrada');
                if ($referer) {
                    return $this->redirect($referer);
                } else {
                    return $this->redirectToRoute('app_home');
                }
            }

            // $existingOpinion = $subject->getOpinions()->filter(function($opinion) use ($userId) {
            //     return $opinion->getOwner()->getId() == $userId;
            // })->first();

            $existingOpinion = $subject->getOpinions()->filter(function($opinion) use ($userId) {
                return $opinion->getOwner()->getId() == $userId && $opinion->getProfessor() === null;
            })->first();
            
            // $opinionExists = $this->opinionRepository->existsByUserSubjectAndNoProfessor($user, $subject);
            
            if ($existingOpinion) {
                return $this->redirectToRoute('app_edit_opinion', [
                    'id' => $existingOpinion->getId()
                ]);
            }

            $opinion->setSubject($subject);

            $object = $subject;
        }

        $opinion->setOwner($user);
    
        $form = $this->createForm(\App\Form\SpecificOpinionFormType::class, $opinion);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            // if there is no comment we dont need to review it
            if($opinion->getComment() == null){
                $opinion->setAccepted(true);
                $opinion->setReviewed(true);
            }
            //it wont display because the query that brings the
            // opinions to frontend filters by comment not null
            
            $this->entityManager->persist($opinion);
            $this->entityManager->flush();

            if ($referer) {
                return $this->redirect($referer);
            } else {
                return $this->redirectToRoute('app_home');
            }
        }

        return $this->render('opinion/new_specific.html.twig', [
            'form' => $form,
            'object' => $object,
        ]);
    }
}
