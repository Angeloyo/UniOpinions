<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class DegreesController extends AbstractController
{
    #[Route('/degrees', name: 'app_degrees')]
    public function index(): Response
    {
        return $this->render('degrees/index.html.twig', [
            'controller_name' => 'DegreesController',
        ]);
    }
}
