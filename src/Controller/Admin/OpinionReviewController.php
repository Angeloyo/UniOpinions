<?php

namespace App\Controller\Admin;

use App\Entity\Professor;
use App\Entity\Subject;
use App\Entity\University;
use App\Entity\Degree;
use App\Entity\Opinion;
use App\Entity\User;
use EasyCorp\Bundle\EasyAdminBundle\Config\Dashboard;
use EasyCorp\Bundle\EasyAdminBundle\Config\MenuItem;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractDashboardController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\RedirectResponse;
use App\Repository\OpinionRepository;
use Doctrine\ORM\EntityManagerInterface;

class OpinionReviewController extends AbstractDashboardController
{
    private $opinionRepository;
    private $entityManager;
    public function __construct(
        OpinionRepository $opinionRepository,
        EntityManagerInterface $entityManager
        )
    {
        $this->opinionRepository = $opinionRepository;
        $this->entityManager = $entityManager;
    }
    
    #[Route('/admin/opinions/unreviewed', name: 'admin_unreviewed_opinions')]
    public function showUnreviewed(): Response
    {
        $opinions = $this->opinionRepository->findBy(['reviewed' => false]);

        return $this->render('admin/unreviewed.html.twig', [
            'opinions' => $opinions,
        ]);
    }

    #[Route('/admin/opinions/{id}/accept', name: 'admin_accept_opinion')]
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

    #[Route('/admin/opinions/{id}/reject', name: 'admin_reject_opinion')]
    public function rejectOpinion(int $id): RedirectResponse
    {
        $opinion = $this->opinionRepository->find($id);
        if ($opinion) {
            $opinion->setAccepted(false);
            $opinion->setReviewed(true);

            $this->entityManager->persist($opinion);
            $this->entityManager->flush();
        }

        return $this->redirectToRoute('admin_unreviewed_opinions');
    }
    
    public function configureDashboard(): Dashboard
    {
        return Dashboard::new()
            ->setTitle('Uniopinions Admin Page');
    }

    public function configureMenuItems(): iterable
    {
        yield MenuItem::linkToDashboard('Dashboard', 'fa fa-home');
        yield MenuItem::linkToCrud('Universities', 'fa fa-building-columns', University::class);
        yield MenuItem::linkToCrud('Degrees', 'fa fa-book', Degree::class);
        yield MenuItem::linkToCrud('Subjects', 'fa fa-calculator', Subject::class);
        yield MenuItem::linkToCrud('Professors', 'fa fa-user-tie', Professor::class);
        yield MenuItem::linkToCrud('Opinions', 'fa fa-comment', Opinion::class);
        yield MenuItem::linkToCrud('Users', 'fa fa-user', User::class);
        yield MenuItem::linkToRoute('Unreviewed Opinions', 'fas fa-glasses', 'admin_unreviewed_opinions');
    }
}
