<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use App\Entity\Opinion;
use App\Entity\User;
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

    #[Route('/opinion/new/{type}/{objectId}/{userId}', name: 'app_create_opinion')]
    public function createOpinion(
        string $type, 
        int $objectId,
        int $userId,
        Request $request,
        ): Response
    {
        $this->denyAccessUnlessGranted('ROLE_USER');
        // $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');

        // Avoid publish comments as another user
        if($this->getUser()->getId() !== $userId){
            $session = $request->getSession();
            $referer = $session->get('referer');
            $this->addFlash('error', 'No puedes publicar comentarios como otro usuario.');
            return $this->redirect($referer);
        }

        $opinion = new Opinion();

        $user = $this->entityManager->getRepository(User::class)->find($userId);

        if (!$user->isVerified()) {
            $session = $request->getSession();
            $referer = $session->get('referer');
            // $this->addFlash('error', 'Tu cuenta no esta verificada y no puedes publicar comentarios. Por favor, verificala mediante el enlace que hemos enviado a tu e-mail.');
            return $this->redirect($referer);
        }

        if ($type == 'professor') {

            $professor = $this->professorRepository->find($objectId);
            if (!$professor) {
                throw $this->createNotFoundException('Profesor no encontrado');
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

        } elseif ($type == 'subject') {

            $subject = $this->subjectRepository->find($objectId);
            if (!$subject) {
                throw $this->createNotFoundException('Subject not found');
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

            $session = $request->getSession();
            $referer = $session->get('referer');
            // $session->remove('referer');
            // return $this->redirect($referer);

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