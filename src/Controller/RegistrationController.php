<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\RegistrationFormType;
use App\Repository\UserRepository;
use App\Security\EmailVerifier;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mime\Address;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Translation\TranslatorInterface;
use SymfonyCasts\Bundle\VerifyEmail\Exception\VerifyEmailExceptionInterface;
use Symfony\Component\Mailer\MailerInterface;

class RegistrationController extends AbstractController
{
    private EmailVerifier $emailVerifier;
    private $mailer;

    public function __construct(
        EmailVerifier $emailVerifier,
        MailerInterface $mailer
    )
    {
        $this->emailVerifier = $emailVerifier;
        $this->mailer = $mailer;
    }

    #[Route('/register', name: 'app_register')]
    public function register(Request $request, UserPasswordHasherInterface $userPasswordHasher, EntityManagerInterface $entityManager): Response
    {
        $user = new User();
        $form = $this->createForm(RegistrationFormType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // encode the plain password
            $user->setPassword(
                $userPasswordHasher->hashPassword(
                    $user,
                    $form->get('plainPassword')->getData()
                )
            );

            $entityManager->persist($user);
            $entityManager->flush();

            // generate a signed url and email it to the user
            $this->emailVerifier->sendEmailConfirmation('app_verify_email', $user,
                (new TemplatedEmail())
                    ->from(new Address('noreply@uniopinions.com', 'UniOpinions'))
                    ->to($user->getEmail())
                    ->subject('Por favor confirma tu email')
                    ->htmlTemplate('registration/confirmation_email.html.twig')
            );

            // return $this->redirectToRoute('app_home');
            // $session = $request->getSession();
            // $referer = $session->get('referer');
            // $session->remove('referer');
            // if ($referer) {
            //     return $this->redirect($referer);
            // } else {
            //     return $this->redirectToRoute('app_home');
            // }
            return $this->redirectToRoute('app_verify_warning');
        }

        return $this->render('registration/register.html.twig', [
            'registrationForm' => $form->createView(),
        ]);
    }

    #[Route('/verify/email', name: 'app_verify_email')]
    public function verifyUserEmail(Request $request, TranslatorInterface $translator, UserRepository $userRepository): Response
    {
        $id = $request->query->get('id');

        if (null === $id) {
            return $this->redirectToRoute('app_register');
        }

        $user = $userRepository->find($id);

        if (null === $user) {
            return $this->redirectToRoute('app_register');
        }

        // validate email confirmation link, sets User::isVerified=true and persists
        try {
            $this->emailVerifier->handleEmailConfirmation($request, $user);
        } catch (VerifyEmailExceptionInterface $exception) {
            $this->addFlash('verify_email_error', $translator->trans($exception->getReason(), [], 'VerifyEmailBundle'));

            return $this->redirectToRoute('app_register');
        }

        // @TODO Change the redirect on success and handle or remove the flash message in your templates
        $this->addFlash('success', 'Tu cuenta ha sido verificada. Ya puedes publicar opiniones.');

        return $this->redirectToRoute('app_home');
    }

    #[Route('/verifywarning', name: 'app_verify_warning')]
    public function pleaseVerify(): Response
    {
        return $this->render('registration/verify_warning.html.twig', [
        ]);
    }
}
