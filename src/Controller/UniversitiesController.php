<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Repository\UniversityRepository;
use App\Repository\DegreeRepository;

class UniversitiesController extends AbstractController
{

    private $universityRepository;
    private $degreeRepository;

    public function __construct(
        UniversityRepository $universityRepository,
        DegreeRepository $degreeRepository,
        )
    {
        $this->universityRepository = $universityRepository;
        $this->degreeRepository = $degreeRepository;
    }
    
    #[Route('/universities', name: 'app_universities')]
    public function index(): Response
    {
        $universities = $this->universityRepository->findAll();

        return $this->render('universities.html.twig', [
            'universities' => $universities,
        ]);
    }

    #[Route('/{universitySlug}', name: 'app_university')]
    public function show(string $universitySlug): Response
    {
        $university = $this->universityRepository->findOneBySlug($universitySlug);

        $degrees = $this->degreeRepository->findBy(['university' => $university]);

        return $this->render('show_university.html.twig', [
            'university' => $university,
            'degrees' => $degrees,
        ]);
    }
}