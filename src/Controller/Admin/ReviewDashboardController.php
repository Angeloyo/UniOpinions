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
use App\Repository\RelationSubjectProfessorRepository;
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
    private $relationSubjectProfessorRepository;
    private $entityManager;
    public function __construct(
        OpinionRepository $opinionRepository,
        UniversityRepository $universityRepository,
        DegreeRepository $degreeRepository,
        SubjectRepository $subjectRepository,
        ProfessorRepository $professorRepository,
        RelationSubjectProfessorRepository $relationSubjectProfessorRepository,
        EntityManagerInterface $entityManager
        )
    {
        $this->opinionRepository = $opinionRepository;
        $this->universityRepository = $universityRepository;
        $this->degreeRepository = $degreeRepository;
        $this->subjectRepository = $subjectRepository;
        $this->professorRepository = $professorRepository;
        $this->relationSubjectProfessorRepository = $relationSubjectProfessorRepository;
        $this->entityManager = $entityManager;
    }
    
    #[Route('/review/unreviewed/opinions', name: 'admin_unreviewed_opinions')]
    public function showUnreviewedOpinions(): Response
    {
        $opinions = $this->opinionRepository->findBy(['accepted' => false]);

        return $this->render('admin/unreviewed_opinions.html.twig', [
            'opinions' => $opinions,
        ]);
    }

    #[Route('/review/unreviewed/universities',name: 'admin_unreviewed_universities')]
    public function showUnreviewedUniversities(): Response
    {
        $universities = $this->universityRepository->findBy(['accepted' => false]);

        return $this->render('admin/unreviewed_universities.html.twig', [
            'universities' => $universities,
        ]);
    }

    #[Route('/review/unreviewed/degrees', name: 'admin_unreviewed_degrees')]
    public function showUnreviewedDegrees(): Response
    {
        $degrees = $this->degreeRepository->findBy(['accepted' => false]);

        return $this->render('admin/unreviewed_degrees.html.twig', [
            'degrees' => $degrees,
        ]);
    }

    #[Route('/review/unreviewed/subjects', name: 'admin_unreviewed_subjects')]
    public function showUnreviewedSubjects(): Response
    {
        $subjects = $this->subjectRepository->findBy(['accepted' => false]);

        return $this->render('admin/unreviewed_subjects.html.twig', [
            'subjects' => $subjects,
        ]);
    }

    #[Route('/review/unreviewed/professors', name: 'admin_unreviewed_professors')]
    public function showUnreviewedProfessors(): Response
    {
        $professors = $this->professorRepository->findBy(['accepted' => false]);

        return $this->render('admin/unreviewed_professors.html.twig', [
            'professors' => $professors,
        ]);
    }

    #[Route('/review/unreviewed/relations-sp', name: 'admin_unreviewed_relations_sp')]
    public function showUnreviewedRelationsSP(): Response
    {
        $relations = $this->relationSubjectProfessorRepository->findBy(['accepted' => false]);

        return $this->render('admin/unreviewed_relations_sp.html.twig', [
            'relations' => $relations,
        ]);
    }

    // OPINIONS

    #[Route('/review/accept/opinion/{id}', name: 'admin_accept_opinion')]
    public function acceptOpinion(int $id): RedirectResponse
    {
        $opinion = $this->opinionRepository->find($id);
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
        $opinion = $this->opinionRepository->find($id);
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
        $university = $this->universityRepository->find($id);
        if ($university) {
            $university->setAccepted(true);
            // $university->setReviewed(true);

            $this->entityManager->persist($university);
            $this->entityManager->flush();
        }

        return $this->redirectToRoute('admin_unreviewed_universities');
    }

    #[Route('/review/reject/university/{id}', name: 'admin_reject_university')]
    public function rejectUniversity(int $id): RedirectResponse
    {
        $university = $this->universityRepository->find($id);
        if ($university) {
            $this->entityManager->remove($university);
            $this->entityManager->flush();
        }

        return $this->redirectToRoute('admin_unreviewed_universities');
    }

    // DEGREES

    #[Route('/review/accept/degree/{id}', name: 'admin_accept_degree')]
    public function acceptDegree(int $id): RedirectResponse
    {
        $degree = $this->degreeRepository->find($id);
        
        if ($degree) {
            $degree->setAccepted(true);
            // $degree->setReviewed(true);

            $this->entityManager->persist($degree);
            $this->entityManager->flush();
        }

        return $this->redirectToRoute('admin_unreviewed_degrees');
    }

    #[Route('/review/reject/degree/{id}', name: 'admin_reject_degree')]
    public function rejectDegree(int $id): RedirectResponse
    {
        $degree = $this->degreeRepository->find($id);
        if ($degree) {
            $this->entityManager->remove($degree);
            $this->entityManager->flush();
        }

        return $this->redirectToRoute('admin_unreviewed_degrees');
    }

    // SUBJECTS

    #[Route('/review/accept/subject/{id}', name: 'admin_accept_subject')]
    public function acceptSubject(int $id): RedirectResponse
    {
        $subject = $this->subjectRepository->find($id);
        if ($subject) {
            $subject->setAccepted(true);
            // $subject->setReviewed(true);

            $this->entityManager->persist($subject);
            $this->entityManager->flush();
        }

        return $this->redirectToRoute('admin_unreviewed_subjects');
    }

    #[Route('/review/reject/subject/{id}', name: 'admin_reject_subject')]
    public function rejectSubject(int $id): RedirectResponse
    {
        $subject = $this->subjectRepository->find($id);
        if ($subject) {
            $this->entityManager->remove($subject);
            $this->entityManager->flush();
        }

        return $this->redirectToRoute('admin_unreviewed_subjects');
    }

    // PROFESSORS

    #[Route('/review/accept/professor/{id}', name: 'admin_accept_professor')]
    public function acceptProfessor(int $id): RedirectResponse
    {
        $professor = $this->professorRepository->find($id);
        if ($professor) {
            $professor->setAccepted(true);
            // $professor->setReviewed(true);

            $this->entityManager->persist($professor);
            $this->entityManager->flush();
        }

        return $this->redirectToRoute('admin_unreviewed_professors');
    }

    #[Route('/review/reject/professor/{id}', name: 'admin_reject_professor')]
    public function rejectProfessor(int $id): RedirectResponse
    {
        $professor = $this->professorRepository->find($id);
        if ($professor) {
            $this->entityManager->remove($professor);
            $this->entityManager->flush();
        }

        return $this->redirectToRoute('admin_unreviewed_professors');
    }

    // RELATIONS SUBJECT-PROFESSOR

    #[Route('/review/accept/relation-sp/{id}', name: 'admin_accept_relation_sp')]
    public function acceptRelationSP(int $id): RedirectResponse
    {
        $relation_sp = $this->relationSubjectProfessorRepository->find($id);
        if ($relation_sp) {
            $relation_sp->setAccepted(true);

            $this->entityManager->persist($relation_sp);
            $this->entityManager->flush();
        }

        return $this->redirectToRoute('admin_unreviewed_relations_sp');
    }

    #[Route('/review/reject/relation-sp/{id}', name: 'admin_reject_relation_sp')]
    public function rejectRelationSP(int $id): RedirectResponse
    {
        $relation_sp = $this->relationSubjectProfessorRepository->find($id);
        if ($relation_sp) {
            $this->entityManager->remove($relation_sp);
            $this->entityManager->flush();
        }

        return $this->redirectToRoute('admin_unreviewed_relations_sp');
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
        yield MenuItem::linkToRoute('Unreviewed relations subject-professor', 'fas fa-key ', 'admin_unreviewed_relations_sp');
    }

    public function configureActions(): Actions
    {
        return parent::configureActions()
            ->add(Crud::PAGE_INDEX, Action::DETAIL);
    }
}
