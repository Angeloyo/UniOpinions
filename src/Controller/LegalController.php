<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class LegalController extends AbstractController
{
    #[Route('/legal/privacy-policy', name: 'app_legal_privacy_policy')]
    public function indexPrivacyPolicy(): Response
    {
        return $this->render('legal/Politica-de-Privacidad.html', [
        ]);
    }

    #[Route('/legal/cookies-policy', name: 'app_legal_cookiespolicy')]
    public function indexCookiesPolicy(): Response
    {
        return $this->render('legal/Politica-de-cookies.html', [
        ]);
    }

    #[Route('/legal/legal-notice', name: 'app_legal_legalnotices')]
    public function indexLegalNotice(): Response
    {
        return $this->render('legal/Aviso-legal.html', [
        ]);
    }
}
