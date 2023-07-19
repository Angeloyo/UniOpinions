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

    #[Route('/opinion/new/{type}/{id}/{userId}', name: 'app_create_opinion')]
    public function createOpinion(
        string $type, 
        int $id,
        int $userId,
        Request $request,
        ): Response
    {
        $this->denyAccessUnlessGranted('ROLE_USER');

        $opinion = new Opinion();

        $user = $this->entityManager->getRepository(User::class)->find($userId);

        if ($type == 'professor') {

            $professor = $this->professorRepository->find($id);
            if (!$professor) {
                throw $this->createNotFoundException('Profesor no encontrado');
            }
            $opinion->setProfessor($professor);

            $object = $professor;

        } elseif ($type == 'subject') {

            $subject = $this->subjectRepository->find($id);
            if (!$subject) {
                throw $this->createNotFoundException('Subject not found');
            }
            $opinion->setSubject($subject);

            $object = $subject;
            
        }

        $opinion->setOwner($user);
    
        $form = $this->createForm(\App\Form\OpinionFormType::class, $opinion);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            
            $this->entityManager->persist($opinion);
            $this->entityManager->flush();

            $session = $request->getSession();

            $referer = $session->get('referer');
            
            $session->remove('referer');

            return $this->redirect($referer);
        }

        return $this->render('opinion/new.html.twig', [
            'form' => $form,
            'object' => $object,
        ]);
    }

    #[Route('/opinion/redirect/{type}/{id}', name: 'app_redirect_opinion_form')]
    public function redirectToOpinionForm($type, $id, Request $request)
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
            'id' => $id,
            'userId' => $this->getUser()->getId()
        ]);
    }

}
