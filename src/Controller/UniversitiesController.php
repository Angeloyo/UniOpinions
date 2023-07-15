<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class UniversitiesController extends AbstractController
{
    #[Route('/universities', name: 'app_universities')]
    public function index(): Response
    {
        return $this->render('universities/index.html.twig', [
            'controller_name' => 'UniversitiesController',
        ]);
    }
}
