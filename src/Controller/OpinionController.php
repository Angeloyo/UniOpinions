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
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;

class OpinionController extends AbstractController
{
    // #[Route('/opinion', name: 'app_opinion')]
    // public function index(): Response
    // {
    //     return $this->render('opinion/index.html.twig', [
    //         'controller_name' => 'OpinionController',
    //     ]);
    // }

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

    #[Route('/opinion/new/{type}/{id}', name: 'app_create_opinion')]
    public function createOpinion(
        string $type, 
        int $id,
        Request $request, 
        ): Response
    {
        $opinion = new Opinion();

        // $testUser = $this->userRepository->find(1);
        $testUser = $this->entityManager->getRepository(User::class)->find(1);

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

        $opinion->setOwner($testUser);
    
        $form = $this->createForm(\App\Form\OpinionFormType::class, $opinion);
    
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            
            $this->entityManager->persist($opinion);
            $this->entityManager->flush();

            return $this->redirectToRoute('app_home');
        }

        return $this->render('opinion/new.html.twig', [
            'form' => $form,
            'object' => $object,
        ]);
    }
}
