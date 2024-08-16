<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\UserType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class SecurityController extends AbstractController
{
    #[Route('/signup', name: 'app_register')]
    // обробка форми реєстрації
    public function register(
        UserPasswordHasherInterface $passwordHasher,
        Request $request,
        Security $security,
        EntityManagerInterface $entityManager
    ): Response {
        $form = $this->createForm(UserType::class, $user = new User());
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $user->setPassword($passwordHasher->hashPassword($user, $user->getPassword()));
            $entityManager->persist($user);
            $entityManager->flush();

            // одразу робимо аутентифікацію новоствореного користувача з повертанням Response:
//            return $security->login($user);
            // АБО можна переадресувати на login сторінку з повідомленням пройти аутентифікацію явно:
            $this->addFlash('success', 'Account created successfully. Please log in');
            return $this->redirectToRoute('app_login');
        }

        return $this->render('security/signup.html.twig', [
            'form' => $form,
        ]);
    }
    #[Route('/login', name: 'app_login')]
    // рендерінг форми для аутентифікації
    public function login(
        AuthenticationUtils $authenticationUtils
    ): Response {
        $error = $authenticationUtils->getLastAuthenticationError();
        $lastUsername = $authenticationUtils->getLastUsername();

        return $this->render('security/login.html.twig', [
            'last_username' => $lastUsername,
            'error' => $error,
        ]);
    }
    }