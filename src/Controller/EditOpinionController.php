<?php

namespace App\Controller;

use App\Repository\OpinionRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\Professor;
use App\Entity\Subject;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\Mime\Address;

class EditOpinionController extends AbstractController
{

    private $entityManager;
    private $opinionRepository;
    private $mailer;

    public function __construct(
        EntityManagerInterface $entityManager,
        OpinionRepository $opinionRepository,
        MailerInterface $mailer
        )
    {
        $this->entityManager = $entityManager;
        $this->opinionRepository = $opinionRepository;
        $this->mailer = $mailer;
    }

    #[Route('/opinion/edit/{id}', name: 'app_edit_opinion')]
    public function index(int $id, Request $request): Response
    {

        $opinion = $this->opinionRepository->find($id);

        $session = $request->getSession();
        $referer = $session->get('referer');

        if (!$opinion) {
            $this->addFlash('error', 'Opinion no encontrada');
            if ($referer) {
                return $this->redirect($referer);
            } else {
                return $this->redirectToRoute('app_home');
            }
        }

        // Check if the user has permission to edit the opinion
        $user = $this->getUser();
        if (!$user || $user->getId() !== $opinion->getOwner()->getId()) {
            // throw $this->createAccessDeniedException('No tienes permiso para editar esta opinion.');
            $this->addFlash('error', 'No tienes permiso para editar esta opinion.');
            if ($referer) {
                return $this->redirect($referer);
            } else {
                return $this->redirectToRoute('app_home');
            }
        }

        $relatedObject = $opinion->getProfessor() ?? $opinion->getSubject();

        if($relatedObject instanceof Professor){
            //editar opinion de un profesor
            $form = $this->createForm(\App\Form\SpecificProfessorOpinionFormType::class, $opinion);
        }
        else if($relatedObject instanceof Subject){
            //Editar opinion de una asignatura
            $form = $this->createForm(\App\Form\SpecificSubjectOpinionFormType::class, $opinion);
        }
        
        // $form = $this->createForm(\App\Form\SpecificOpinionFormType::class, $opinion);

        // Get original comment before form handling
        $originalComment = $opinion->getComment();

        $form->handleRequest($request);

        if ($form->isSubmitted() ) {
            //comprobar que se ha rellenado al menos uno de los campos

            $checkScore = $form->get('givenScore')->getData();
            $checkComment = $form->get('comment')->getData();
            //$checkKeywords = $form->get('keywords')->getData();

            $errors = [];

            if ($checkScore === null && $checkComment === null) {
                $errors['input'] = 'Debes rellenar al menos uno de los campos: valoración general, comentario';
            }

            if (count($errors) > 0) {
                // Render the form again and pass the errors
                return $this->render('edit_opinion/index.html.twig', [
                    'form' => $form,
                    'object' => $opinion->getSubject() ?? $opinion->getProfessor(),
                    'errors' => $errors,
                ]);

            } 
            //si no hay errores
            else{

                // if there is no comment we dont need to review it
                if($opinion->getComment() == null){
                    $opinion->setAccepted(true);
                    $opinion->setReviewed(true);
                }
                else{
                    // When comment is edited, it needs to be reviewed again
                    if ($originalComment != $opinion->getComment()) {
                        //hacer: avisar al usuario de que el comentario ha sido enviado a revision
                        $opinion->setAccepted(false);
                        $opinion->setReviewed(false);

                        //send alert email to admin 
                        $email = (new TemplatedEmail())
                            ->from(new Address('noreply@uniopinions.com', 'UniOpinions'))
                            ->to('uniopinionsdotcom@gmail.com')
                            ->subject('New opinion to be reviewed.')
                            ->htmlTemplate('opinion/email_newopinion_admin.html.twig')
                            ->context([
                                'info' => "edited opinion",
                            ])
                        ;

                        $this->mailer->send($email);
                    }
                }

                $this->entityManager->persist($opinion);
                $this->entityManager->flush();

                if ($referer) {
                    return $this->redirect($referer);
                } else {
                    return $this->redirectToRoute('app_home');
                }
            }
        }

        return $this->render('edit_opinion/index.html.twig', [
            'form' => $form,
            'object' => $relatedObject,
        ]);
    }
}
