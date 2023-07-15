<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\University;
use App\Entity\Degree;

class UniversitiesController extends AbstractController
{
    #[Route('/universities', name: 'app_universities')]
    public function index(EntityManagerInterface $entityManager): Response
    {
        $universities = $entityManager->getRepository(University::class)->findAll();

        return $this->render('universities/index.html.twig', [
            'universities' => $universities,
        ]);
    }

    #[Route('/{universitySlug}', name: 'app_university')]
    public function show(string $universitySlug, EntityManagerInterface $entityManager): Response
    {
        $university = $entityManager->getRepository(University::class)
            ->findOneBy(['slug' => $universitySlug]);

        $degrees = $entityManager->getRepository(Degree::class)
            ->findBy(['university' => $university]);

        if (!$university) {
            throw $this->createNotFoundException('La universidad especificada no existe');
        }

        return $this->render('universities/show.html.twig', [
            'university' => $university,
            'degrees' => $degrees,
        ]);
    }
}