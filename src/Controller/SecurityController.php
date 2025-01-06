<?php

namespace App\Controller;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Lexik\Bundle\JWTAuthenticationBundle\Services\JWTTokenManagerInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

class SecurityController extends AbstractController
{
    #[Route(path: '/login', name: 'app_login')]
    public function login(AuthenticationUtils $authenticationUtils, JWTTokenManagerInterface $jwtManager): Response
    {
        if ($this->getUser()) {
            $token = $jwtManager->create($this->getUser());

            if ($this->isGranted('ROLE_ADMIN')) {
                return $this->redirectToRoute('admin', ['token' => $token]);
            } elseif ($this->isGranted('ROLE_INSTRUCTOR')) {
                return $this->redirectToRoute('instructor_dashboard', ['token' => $token]);
            }

            // Redirection par défaut si l'utilisateur n'a ni le rôle admin ni instructeur
            return $this->redirectToRoute('app_home', ['token' => $token]);
        }

        // get the login error if there is one
        $error = $authenticationUtils->getLastAuthenticationError();

        // last username entered by the user
        $lastUsername = $authenticationUtils->getLastUsername();

        return $this->render('security/login.html.twig', [
            'last_username' => $lastUsername,
            'error' => $error,
        ]);
    }

    // Pour Login react 
    #[Route(path: '/api/login_check', name: 'api_login_check', methods: ['POST'])]
    public function apiLogin(JWTTokenManagerInterface $jwtManager): JsonResponse
    {
        $user = $this->getUser();
        if (!$user) {
            return new JsonResponse(['error' => 'Invalid credentials'], 401);
        }

        $token = $jwtManager->create($user);
        return new JsonResponse(['token' => $token]);
    }
    #[Route(path: '/api/logout', name: 'api_logout', methods: ['POST'])]
    public function apiLogout(): JsonResponse
    {
        // La déconnexion est gérée par le firewall de Symfony
        return new JsonResponse(['message' => 'Déconnexion réussie']);
    }


    #[Route(path: '/logout', name: 'app_logout', methods: ['GET', 'POST'])]
    public function logout(): void
    {
        // Cette méthode peut rester vide.
        // Symfony gère automatiquement la déconnexion.
    }
}
