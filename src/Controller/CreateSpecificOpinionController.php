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

    #[Route('/opinion/new-specific/{type}/{objectId}', name: 'app_create_specific_opinion')]
    public function createSpecificOpinion(
        string $type, 
        int $objectId,
        Request $request,
        ): Response
    {
        $this->denyAccessUnlessGranted('ROLE_USER');

        $user = $this->getUser();
        $session = $request->getSession();
        $referer = $session->get('referer');

        if (!$user->isVerified()) {
            return $this->redirect($referer);
        }
        
        $opinion = new Opinion();
        $userId = $this->getUser()->getId();

        if ($type == 'p') {

            $professor = $this->professorRepository->find($objectId);
            if (!$professor) {
                // throw $this->createNotFoundException('Profesor no encontrado');
                $this->addFlash('error', 'Profesor no encontrado.');
                return $this->redirect($referer);
            }
            
            $existingOpinion = $professor->getOpinions()->filter(function($opinion) use ($userId) {
                return $opinion->getOwner()->getId() == $userId;
            })->first();

            if ($existingOpinion) {
                return $this->redirectToRoute('app_edit_opinion', [
                    'id' => $existingOpinion->getId()
                ]);
            }

            $opinion->setProfessor($professor);

            $object = $professor;

        } elseif ($type == 's') {

            $subject = $this->subjectRepository->find($objectId);
            if (!$subject) {
                // throw $this->createNotFoundException('Subject not found');
                $this->addFlash('error', 'Asignatura no encontrada');
                return $this->redirect($referer);
            }

            $existingOpinion = $subject->getOpinions()->filter(function($opinion) use ($userId) {
                return $opinion->getOwner()->getId() == $userId;
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
