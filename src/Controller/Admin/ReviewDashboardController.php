<?php

namespace App\Controller\Admin;

use EasyCorp\Bundle\EasyAdminBundle\Config\Dashboard;
use EasyCorp\Bundle\EasyAdminBundle\Config\MenuItem;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractDashboardController;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Repository\UniversityRepository;
use App\Repository\DegreeRepository;
use App\Repository\OpinionRepository;
use App\Repository\SubjectRepository;
use App\Repository\ProfessorRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;

class ReviewDashboardController extends AbstractDashboardController
{
    #[Route('/review', name: 'review')]
    public function index(): Response
    {
        return $this->render('admin/index.html.twig');
    }

    private $opinionRepository;
    private $universityRepository;
    private $degreeRepository;
    private $subjectRepository;
    private $professorRepository;
    private $entityManager;
    public function __construct(
        OpinionRepository $opinionRepository,
        UniversityRepository $universityRepository,
        DegreeRepository $degreeRepository,
        SubjectRepository $subjectRepository,
        ProfessorRepository $professorRepository,
        EntityManagerInterface $entityManager
        )
    {
        $this->opinionRepository = $opinionRepository;
        $this->universityRepository = $universityRepository;
        $this->degreeRepository = $degreeRepository;
        $this->subjectRepository = $subjectRepository;
        $this->professorRepository = $professorRepository;
        $this->entityManager = $entityManager;
    }
    
    #[Route('/review/unreviewed/opinions', name: 'admin_unreviewed_opinions')]
    public function showUnreviewedOpinions(): Response
    {
        $opinions = $this->opinionRepository->findBy(['reviewed' => false]);

        return $this->render('admin/unreviewed_opinions.html.twig', [
            'opinions' => $opinions,
        ]);
    }

    #[Route('/review/unreviewed/universities',name: 'admin_unreviewed_universities')]
    public function showUnreviewedUniversities(): Response
    {
        $universities = $this->universityRepository->findBy(['reviewed' => false]);

        return $this->render('admin/unreviewed_universities.html.twig', [
            'universities' => $universities,
        ]);
    }

    #[Route('/review/unreviewed/degrees', name: 'admin_unreviewed_degrees')]
    public function showUnreviewedDegrees(): Response
    {
        $degrees = $this->degreeRepository->findBy(['reviewed' => false]);

        return $this->render('admin/unreviewed_degrees.html.twig', [
            'degrees' => $degrees,
        ]);
    }

    #[Route('/review/unreviewed/subjects', name: 'admin_unreviewed_subjects')]
    public function showUnreviewedSubjects(): Response
    {
        $subjects = $this->subjectRepository->findBy(['reviewed' => false]);

        return $this->render('admin/unreviewed_subjects.html.twig', [
            'subjects' => $subjects,
        ]);
    }

    #[Route('/review/unreviewed/professors', name: 'admin_unreviewed_professors')]
    public function showUnreviewedProfessors(): Response
    {
        $professors = $this->professorRepository->findBy(['reviewed' => false]);

        return $this->render('admin/unreviewed_professors.html.twig', [
            'professors' => $professors,
        ]);
    }

    // OPINIONS

    #[Route('/review/accept/degree/{id}', name: 'admin_accept_opinion')]
    public function acceptOpinion(int $id): RedirectResponse
    {
        $opinion = $this->opinionRepository->findBy(['id' => $id]);
        if ($opinion) {
            $opinion->setAccepted(true);
            $opinion->setReviewed(true);

            $this->entityManager->persist($opinion);
            $this->entityManager->flush();
        }

        return $this->redirectToRoute('admin_unreviewed_opinions');
    }

    #[Route('/review/reject/opinion/{id}', name: 'admin_reject_opinion')]
    public function rejectOpinion(int $id): RedirectResponse
    {
        $opinion = $this->opinionRepository->findBy(['id' => $id]);
        if ($opinion) {
            $opinion->setAccepted(false);
            $opinion->setReviewed(true);
            // no se elimina opinion para que cuente el emoticono
            // y palabras clave

            $this->entityManager->persist($opinion);
            $this->entityManager->flush();
        }

        return $this->redirectToRoute('admin_unreviewed_opinions');
    }

    // UNIVERSITIES

    #[Route('/review/accept/university/{id}', name: 'admin_accept_university')]
    public function acceptUniversity(int $id): RedirectResponse
    {
        $university = $this->universityRepository->findBy(['id' => $id]);
        if ($university) {
            $university->setAccepted(true);
            $university->setReviewed(true);

            $this->entityManager->persist($university);
            $this->entityManager->flush();
        }

        return $this->redirectToRoute('admin_unreviewed_universities');
    }

    #[Route('/review/reject/university/{id}', name: 'admin_reject_university')]
    public function rejectUniversity(int $id): RedirectResponse
    {
        $university = $this->universityRepository->findBy(['id' => $id]);
        if ($university) {
            // $university->setAccepted(false);
            // $university->setReviewed(true);
            $this->entityManager->remove($university);
            // $this->entityManager->persist($university);
            $this->entityManager->flush();
        }

        return $this->redirectToRoute('admin_unreviewed_universities');
    }

    // DEGREES

    #[Route('/review/accept/degree/{id}', name: 'admin_accept_degree')]
    public function acceptDegree(int $id): RedirectResponse
    {
        $degree = $this->degreeRepository->findBy(['id' => $id]);
        
        if ($degree) {
            $degree->setAccepted(true);
            $degree->setReviewed(true);

            $this->entityManager->persist($degree);
            $this->entityManager->flush();
        }

        return $this->redirectToRoute('admin_unreviewed_degrees');
    }

    #[Route('/review/reject/degree/{id}', name: 'admin_reject_degree')]
    public function rejectDegree(int $id): RedirectResponse
    {
        $degree = $this->degreeRepository->findBy(['id' => $id]);
        if ($degree) {
            // $university->setAccepted(false);
            // $university->setReviewed(true);
            $this->entityManager->remove($degree);
            // $this->entityManager->persist($university);
            $this->entityManager->flush();
        }

        return $this->redirectToRoute('admin_unreviewed_degrees');
    }

    // SUBJECTS

    #[Route('/review/accept/subject/{id}', name: 'admin_accept_subject')]
    public function acceptSubject(int $id): RedirectResponse
    {
        $subject = $this->subjectRepository->findBy(['id' => $id]);
        if ($subject) {
            $subject->setAccepted(true);
            $subject->setReviewed(true);

            $this->entityManager->persist($subject);
            $this->entityManager->flush();
        }

        return $this->redirectToRoute('admin_unreviewed_subjects');
    }

    #[Route('/review/reject/subject/{id}', name: 'admin_reject_subject')]
    public function rejectSubject(int $id): RedirectResponse
    {
        $subject = $this->subjectRepository->findBy(['id' => $id]);
        if ($subject) {
            // $university->setAccepted(false);
            // $university->setReviewed(true);
            $this->entityManager->remove($subject);
            // $this->entityManager->persist($university);
            $this->entityManager->flush();
        }

        return $this->redirectToRoute('admin_unreviewed_subjects');
    }

    // PROFESSORS

    #[Route('/review/accept/professor/{id}', name: 'admin_accept_professor')]
    public function acceptProfessor(int $id): RedirectResponse
    {
        $professor = $this->professorRepository->findBy(['id' => $id]);
        if ($professor) {
            $professor->setAccepted(true);
            $professor->setReviewed(true);

            $this->entityManager->persist($professor);
            $this->entityManager->flush();
        }

        return $this->redirectToRoute('admin_unreviewed_professors');
    }

    #[Route('/review/reject/professor/{id}', name: 'admin_reject_professor')]
    public function rejectProfessor(int $id): RedirectResponse
    {
        $professor = $this->professorRepository->findBy(['id' => $id]);
        if ($professor) {
            // $university->setAccepted(false);
            // $university->setReviewed(true);
            $this->entityManager->remove($professor);
            // $this->entityManager->persist($university);
            $this->entityManager->flush();
        }

        return $this->redirectToRoute('admin_unreviewed_professors');
    }

    public function configureDashboard(): Dashboard
    {
        return Dashboard::new()
            ->setTitle('Uniopinions Admin Review Page');
    }

    public function configureMenuItems(): iterable
    {
        yield MenuItem::linkToDashboard('Dashboard', 'fa fa-home');
        yield MenuItem::linkToRoute('Unreviewed Opinions', 'fas fa-glasses', 'admin_unreviewed_opinions');
        yield MenuItem::linkToRoute('Unreviewed Universities', 'fas fa-building-columns', 'admin_unreviewed_universities');
        yield MenuItem::linkToRoute('Unreviewed Degrees', 'fas fa-flask', 'admin_unreviewed_degrees');
        yield MenuItem::linkToRoute('Unreviewed Subjects', 'fas fa-book', 'admin_unreviewed_subjects');
        yield MenuItem::linkToRoute('Unreviewed Professors', 'fas fa-chalkboard-user', 'admin_unreviewed_professors');
    }

    public function configureActions(): Actions
    {
        return parent::configureActions()
            ->add(Crud::PAGE_INDEX, Action::DETAIL);
    }
}
