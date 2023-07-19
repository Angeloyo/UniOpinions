<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use App\Entity\Opinion;
use App\Entity\User;
use App\Repository\SubjectRepository;
use App\Repository\ProfessorRepository;
use Doctrine\ORM\EntityManagerInterface;

class OpinionController extends AbstractController
{

    private $professorRepository;
    private $subjectRepository;
    private $userRepository;
    private $entityManager;
    public function __construct(
        ProfessorRepository $professorRepository,
        SubjectRepository $subjectRepository,
        SubjectRepository $userRepository,
        EntityManagerInterface $entityManager
        )
    {
        $this->professorRepository = $professorRepository;
        $this->subjectRepository = $subjectRepository;
        $this->userRepository = $userRepository;
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

        $opinion = new Opinion();

        $user = $this->entityManager->getRepository(User::class)->find($userId);

        if ($type == 'professor') {

            $professor = $this->professorRepository->find($objectId);
            if (!$professor) {
                throw $this->createNotFoundException('Profesor no encontrado');
            }
            
            // $existingOpinion = $professor->getOpinions()->findOneBy([
            //     'owner' => $userId,
            // ]);
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

            // $existingOpinion = $subject->getOpinions()->findOneBy([
            //     'owner' => $userId,
            // ]);
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
    
        $form = $this->createForm(\App\Form\OpinionFormType::class, $opinion);

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

        return $this->render('opinion/new.html.twig', [
            'form' => $form,
            'object' => $object,
        ]);
    }

    #[Route('/opinion/redirect/{type}/{objectId}', name: 'app_redirect_opinion_form')]
    public function redirectToOpinionForm($type, $objectId, Request $request)
    {

        $referer = $request->headers->get('referer');
        $parsedReferer = parse_url($referer);

        if (isset($parsedReferer['path'])) {
            $path = $parsedReferer['path'];
            
            if ($path != '/login' && $path != '/register') {
                $session = $request->getSession();
                $session->set('referer', $referer);
            }
        }

        $this->denyAccessUnlessGranted('ROLE_USER');

        return $this->redirectToRoute('app_create_opinion', [
            'type' => $type,
            'objectId' => $objectId,
            'userId' => $this->getUser()->getId()
        ]);
    }

}
