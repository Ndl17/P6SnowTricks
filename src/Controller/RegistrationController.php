<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\RegistrationFormType;
use App\Repository\UserRepository;
use App\Security\UsersAuthenticator;
use App\Service\JWTService;
use App\Service\SendMailService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\UserAuthenticatorInterface;

class RegistrationController extends AbstractController
{

    
    private $userRepository;
    private $mail;
    private $jwt;
    private $figureService;
    private $entityManager;

    public function __construct(
        UserRepository $userRepository, 
        SendMailService $mail,
        JWTService $jwt,
        EntityManagerInterface $entityManager
        )
    {
        $this->userRepository = $userRepository;
        $this->mail = $mail;
        $this->jwt = $jwt;
        $this->entityManager = $entityManager;

   
    }


    #[Route('/register', name:'app_register')]

    /**
     * Summary of register
     * @param \Symfony\Component\HttpFoundation\Request $request
     * @param \Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface $userPasswordHasher
     * @param \Symfony\Component\Security\Http\Authentication\UserAuthenticatorInterface $userAuthenticator
     * @param \App\Security\UsersAuthenticator $authenticator
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function register(Request $request, UserPasswordHasherInterface $userPasswordHasher, UserAuthenticatorInterface $userAuthenticator, UsersAuthenticator $authenticator): Response
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

            $user->setIsVerified(false);
            $this->entityManager->persist($user);
            $this->entityManager->flush();

            //on génére le jwt de l'utilisateur
            $header = [
                'typ' => 'JWT',
                'alg' => 'HS256',
            ];

            $payload = [
                'user_id' => $user->getId(),
            ];
            // on génere le token

            $token =  $this->jwt->generate($header, $payload, $_ENV['JWT_SECRET']);
            // on envoie le mail de confirmation

            $this->mail->send('noreply@snowtricks.com',
                $user->getEmail(),
                'Activation de votre compte',
                'confirmationRegister',
                compact('user', 'token'));

            $this->addFlash('success', 'Votre compte a bien été inscrit ! vous allez recevoir un e-mail de validation.');

        }

        return $this->render('registration/register.html.twig', [
            'registrationForm' => $form->createView(),
        ]);
    }

    #[Route('/verify/{token}', name:'app_verify')]

    /**
     * Summary of verify
     * @param mixed $token
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function verify(string $token): Response
    {

        // on verifie que le token est valide, pas expiré et pas modif
        if ( $this->jwt->isValid($token) && ! $this->jwt->isExpired($token) &&  $this->jwt->check($token, $this->getParameter('app.jwtsecret'))) {
            // on récupère le payload
            $payload =  $this->jwt->getPayload($token);
            // on récupère l'utilisateur
            $user = $this->userRepository->find($payload['user_id']);
            // on vérifie que l'utilisateur existe et n'a pas activé son compte
            if ($user && !$user->getIsVerified()) {
                // on active le compte
                $user->setIsVerified(true);
                $this->entityManager->persist($user);
                $this->entityManager->flush();
                $this->addFlash('success', 'Votre compte a bien été activé !');
                return $this->redirectToRoute('app_login');

            }
            //ici un problème se pose dans le token

            $this->addFlash('danger', 'Le token est invalide ou a expiré !');
            return $this->redirectToRoute('app_login');
        }
    }

    #[Route('/resend', name:'app_resend')]

    /**
     * Summary of resend
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function resend(): Response
    {

        $user = $this->getUser();

        if (!$user) {
            $this->addFlash('danger', 'Vous devez être connecté pour accéder à cette page !');
            return $this->redirectToRoute('app_login');
        }
        if ($user->getIsVerified()) {
            $this->addFlash('warning', 'Votre compte est déjà activé !');
            return $this->redirectToRoute('app_login');
        }
        //on génére le jwt de l'utilisateur
        $header = [
            'typ' => 'JWT',
            'alg' => 'HS256',
        ];

        $payload = [
            'user_id' => $user->getId(),
        ];
        // on génere le token

        $token =  $this->jwt->generate($header, $payload, $this->getParameter('app.jwtsecret'));
        // on envoie le mail de confirmation

        $this->mail->send('noreply@snowtricks.com',
            $user->getEmail(),
            'Activation de votre compte',
            'confirmationRegister',
            compact('user', 'token'));

        $this->addFlash('success', 'Votre compte a bien été inscrit ! vous allez recevoir un e-mail de validation.');
        return $this->redirectToRoute('app_login');

    }

}
