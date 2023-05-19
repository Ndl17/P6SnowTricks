<?php

namespace App\Controller;

use App\Form\ResetPasswordFormType;
use App\Form\ResetPasswordRequestFormType;
use App\Repository\UserRepository;
use App\Service\SendMailService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Csrf\TokenGenerator\TokenGeneratorInterface;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class SecurityController extends AbstractController
{
    #[Route(path:'/login', name:'app_login')]
    public function login(AuthenticationUtils $authenticationUtils): Response
    {
        // if ($this->getUser()) {
        //     return $this->redirectToRoute('target_path');
        // }

        // get the login error if there is one
        $error = $authenticationUtils->getLastAuthenticationError();
        // last username entered by the user
        $lastUsername = $authenticationUtils->getLastUsername();

        return $this->render('security/login.html.twig', ['last_username' => $lastUsername, 'error' => $error]);
    }

    #[Route(path:'/logout', name:'app_logout')]
    public function logout(): void
    {
        throw new \LogicException ('This method can be blank - it will be intercepted by the logout key on your firewall.');
    }

    #[Route(path:'/forgot', name:'app_forgot')]
    /**
     * Summary of forgotPassword
     * @param \Symfony\Component\HttpFoundation\Request $request
     * @param \App\Repository\UserRepository $userRepository
     * @param \Symfony\Component\Security\Csrf\TokenGenerator\TokenGeneratorInterface $tokenGeneratorInterface
     * @param \Doctrine\ORM\EntityManagerInterface $entityManager
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function forgotPassword(Request $request, UserRepository $userRepository, TokenGeneratorInterface $tokenGeneratorInterface, EntityManagerInterface $entityManager, SendMailService $mail): Response
    {
        $form = $this->createForm(ResetPasswordRequestFormType::class);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $user = $userRepository->findOneByEmail($form->get('email')->getData());
            if ($user) {
                //on génère un token de réinitialisation
                $token = $tokenGeneratorInterface->generateToken();
                //on enregistre le token en base de données
                $user->setResetToken($token);
                $entityManager->persist($user);
                $entityManager->flush();

                //on génère l'url de réinitialisation
                $url = $this->generateUrl('app_reset', ['token' => $token], UrlGeneratorInterface::ABSOLUTE_URL);

                $context = compact('url', 'user');
                $mail->send(
                    'noreply@snowtricks.com',
                    $user->getEmail(),
                    'Réinitialisation de votre mot de passe',
                    'passwordReset',
                    $context

                );
                //   dd($user);
                $this->addFlash('success', 'Un email de réinitialisation de mot de passe vous a été envoyé');
                return $this->redirectToRoute('app_login');

            } else {
                //$user est null
                $this->addFlash('danger', 'Une erreur est survenue, veuillez réessayer');
                return $this->redirectToRoute('app_login');
            }
        }

        return $this->render('security/forgotReset.html.twig', [
            'requestForm' => $form->createView(),
        ]);
    }

    #[Route(path:'/reset/{token}', name:'app_reset')]
    /**
     * Summary of resetPassword
     * @param \Symfony\Component\HttpFoundation\Request $request
     * @param \App\Repository\UserRepository $userRepository
     * @param \Doctrine\ORM\EntityManagerInterface $entityManager
     * @param string $token
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function resetPassword(Request $request, UserRepository $userRepository, EntityManagerInterface $entityManager, string $token, UserPasswordHasherInterface $passwordHasher): Response
    {

        $user = $userRepository->findOneByResetToken($token);
        if ($user) {
            //on génère le formulaire de réinitialisation
            $form = $this->createForm(ResetPasswordFormType::class);
            $form->handleRequest($request);
            if ($form->isSubmitted() && $form->isValid()) {
                //on réinitialise le token
                $user->setResetToken('');
                //on réinitialise le mot de passe
                $user->setPassword(
                    $passwordHasher->hashPassword(
                        $user,
                        $form->get('password')->getData()
                    )
                );
                $entityManager->persist($user);
                $entityManager->flush();
                $this->addFlash('success', 'Votre mot de passe a bien été réinitialisé');
                return $this->redirectToRoute('home');
            }
            return $this->render('security/resetPassword.html.twig', [
                'resetForm' => $form->createView(),
            ]);

        } else {
            $this->addFlash('danger', 'Une erreur est survenue, veuillez réessayer');
            return $this->redirectToRoute('app_login');
        }

    }
}
