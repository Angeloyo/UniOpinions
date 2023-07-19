<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Symfony\Component\HttpFoundation\Request;

class LoginController extends AbstractController
{
    #[Route('/login', name: 'app_login')]
    // public function index(): Response
    public function index(AuthenticationUtils $authenticationUtils): Response
    {
        // return $this->render('login/index.html.twig', [
        //     'controller_name' => 'LoginController',
        // ]);
        
        // get the login error if there is one
        $error = $authenticationUtils->getLastAuthenticationError();

         // last username entered by the user
        $lastUsername = $authenticationUtils->getLastUsername();
        
        return $this->render('login/index.html.twig', [
            'last_username' => $lastUsername,
            'error'         => $error,
        ]);
    }

    #[Route('/loginsuccess', name: 'after_login')]
    // public function index(): Response
    public function loginsuccess(
        AuthenticationUtils $authenticationUtils,
        Request $request): Response
    {
        $session = $request->getSession();

        $referer = $session->get('referer');

        if ($referer) {
            return $this->redirect($referer);
        } else {
            return $this->redirectToRoute('app_home');
        }
    }
    
}
