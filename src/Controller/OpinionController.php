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
        $session = $request->getSession();
        $referer = $session->get('referer');
        
        if (!$this->getUser()->isVerified()) {
            
            if ($referer) {
                return $this->redirect($referer);
            } else {
                return $this->redirectToRoute('app_home');
            }
        }

        return $this->redirectToRoute('app_create_specific_opinion', [
            'type' => $type,
            'objectId' => $objectId,
            // 'userId' => $this->getUser()->getId()
        ]);
    }

    #[Route('/opinion/generic-redirect/', name: 'app_redirect_generic_opinion')]
    public function redirectToGenericOpinionForm($type, $objectId, Request $request)
    {

        $this->denyAccessUnlessGranted('ROLE_USER');
        $session = $request->getSession();
        $referer = $session->get('referer');

        if (!$this->getUser()->isVerified()) {
            
            if ($referer) {
                return $this->redirect($referer);
            } else {
                return $this->redirectToRoute('app_home');
            }
        }

        return $this->redirectToRoute('app_create_generic_opinion', [
            // 'type' => $type,
            // 'objectId' => $objectId,
            // 'userId' => $this->getUser()->getId()
        ]);
    }

}
