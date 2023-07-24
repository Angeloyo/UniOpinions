<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;

class OpinionController extends AbstractController
{
    //This is needed in order to force user to login when he wants to create an opinion
    //and get his id to re route
    #[Route('/opinion/redirect/{type}/{objectId}', name: 'app_redirect_specific_opinion')]
    public function redirectToSpecificOpinionForm($type, $objectId, Request $request)
    {

        $this->denyAccessUnlessGranted('ROLE_USER');
        // $this->denyAccessUnlessGranted('IS_AUTHENTIC ATED_FULLY');

        if (!$this->getUser()->isVerified()) {
            $session = $request->getSession();
            $referer = $session->get('referer');
            return $this->redirect($referer);
        }

        return $this->redirectToRoute('app_create_opinion', [
            'type' => $type,
            'objectId' => $objectId,
            'userId' => $this->getUser()->getId()
        ]);
    }

}
