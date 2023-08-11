<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use App\Entity\Opinion;
use App\Entity\Professor;
use App\Entity\Subject;
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
        Request $request,
        ?int $professorId = null,
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

        //opinion sobre un profesor
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

        } 
        //opinion de una asignatura
        else{

            $subject = $this->subjectRepository->find($subjectId);
            if (!$subject) {
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
            
            if ($existingOpinion) {
                return $this->redirectToRoute('app_edit_opinion', [
                    'id' => $existingOpinion->getId()
                ]);
            }

            $opinion->setSubject($subject);

            $object = $subject;
        }

        $opinion->setOwner($user);

        if($object instanceof Professor){
            //editar opinion de un profesor
            $form = $this->createForm(\App\Form\SpecificProfessorOpinionFormType::class, $opinion);
        }
        else if($object instanceof Subject){
            //Editar opinion de una asignatura
            $form = $this->createForm(\App\Form\SpecificSubjectOpinionFormType::class, $opinion);
        }

        // $form = $this->createForm(\App\Form\SpecificOpinionFormType::class, $opinion);

        $form->handleRequest($request);

        if ($form->isSubmitted() ) {
            //comprobar que se ha rellenado al menos uno de los campos

            $checkScore = $form->get('givenScore')->getData();
            $checkComment = $form->get('comment')->getData();
            $checkKeywords = $form->get('keywords')->getData();

            $errors = [];

            if ($checkScore === null && $checkComment === null && $checkKeywords === null) {
                $errors['input'] = 'Debes rellenar al menos uno de los campos: valoración general, comentario, palabras clave';
            }

            if (count($errors) > 0) {
                // Render the form again and pass the errors
                return $this->render('opinion/new_specific.html.twig', [
                    'form' => $form,
                    'object' => $object,
                    'errors' => $errors,
                ]);

            } 
            //si no hay errores
            else{

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
            
        }

        return $this->render('opinion/new_specific.html.twig', [
            'form' => $form,
            'object' => $object,
        ]);
    }
}
