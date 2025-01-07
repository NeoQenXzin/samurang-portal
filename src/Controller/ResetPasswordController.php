<?php

namespace App\Controller;

use App\Entity\Student;
use App\Entity\Instructor;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Symfony\Component\Security\Csrf\TokenGenerator\TokenGeneratorInterface;

class ResetPasswordController extends AbstractController
{
    #[Route('/api/reset-password/request', name: 'app_reset_password_request', methods: ['POST'])]
    public function request(
        Request $request,
        EntityManagerInterface $entityManager,
        TokenGeneratorInterface $tokenGenerator,
        MailerInterface $mailer
    ): JsonResponse {
        $data = json_decode($request->getContent(), true);
        $email = $data['email'] ?? null;

        if (!$email) {
            return new JsonResponse(['error' => 'Email requis'], Response::HTTP_BAD_REQUEST);
        }

        $user = $entityManager->getRepository(Student::class)->findOneBy(['mail' => $email]);
        if (!$user) {
            $user = $entityManager->getRepository(Instructor::class)->findOneBy(['mail' => $email]);
        }

        if (!$user) {
            return new JsonResponse(['message' => 'Un email a été envoyé si l\'adresse existe dans notre base de données.']);
        }

        $resetToken = $tokenGenerator->generateToken();
        $user->setResetToken($resetToken);
        $user->setResetTokenExpiresAt(new \DateTime('+1 hour'));

        $entityManager->persist($user);
        $entityManager->flush();

        $resetLink = $_ENV['FRONTEND_URL'] . '/reset-password/' . $resetToken;

        $email = (new Email())
            ->from('noreply@votre-domaine.com')
            ->to($user->getMail())
            ->subject('Réinitialisation de votre mot de passe')
            ->html("<p>Pour réinitialiser votre mot de passe, cliquez sur ce lien : <a href='{$resetLink}'>Réinitialiser le mot de passe</a></p>");

        $mailer->send($email);

        return new JsonResponse(['message' => 'Un email a été envoyé avec les instructions.']);
    }

    #[Route('/api/reset-password/reset', name: 'app_reset_password_reset', methods: ['POST'])]
    public function reset(
        Request $request,
        EntityManagerInterface $entityManager,
        UserPasswordHasherInterface $passwordHasher
    ): JsonResponse {
        $data = json_decode($request->getContent(), true);
        $token = $data['token'] ?? null;
        $newPassword = $data['password'] ?? null;

        if (!$token || !$newPassword) {
            return new JsonResponse(['error' => 'Données manquantes'], Response::HTTP_BAD_REQUEST);
        }

        $user = $entityManager->getRepository(Student::class)->findOneBy(['resetToken' => $token]);
        if (!$user) {
            $user = $entityManager->getRepository(Instructor::class)->findOneBy(['resetToken' => $token]);
        }

        if (!$user || $user->getResetTokenExpiresAt() < new \DateTime()) {
            return new JsonResponse(['error' => 'Token invalide ou expiré'], Response::HTTP_BAD_REQUEST);
        }

        $hashedPassword = $passwordHasher->hashPassword($user, $newPassword);
        $user->setPassword($hashedPassword);
        $user->setResetToken(null);
        $user->setResetTokenExpiresAt(null);

        $entityManager->persist($user);
        $entityManager->flush();

        return new JsonResponse(['message' => 'Mot de passe réinitialisé avec succès']);
    }
}
