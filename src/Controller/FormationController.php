<?php

namespace App\Controller;

use App\Entity\Formation;
use App\Entity\Instructor;
use App\Entity\Student;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

class FormationController extends AbstractController
{
    #[Route('/api/formations/{id}/register', name: 'formation_register', methods: ['POST'])]
    public function register(Formation $formation, EntityManagerInterface $em): JsonResponse
    {
        $user = $this->getUser();
        if (!$user instanceof Instructor && !$user instanceof Student) {
            return $this->json(['message' => 'Utilisateur non autorisé'], 403);
        }

        $formation->addParticipant($user);
        $em->flush();

        return $this->json(['message' => 'Inscription réussie']);
    }

    #[Route('/api/formations/{id}/unregister', name: 'formation_unregister', methods: ['POST'])]
    public function unregister(Formation $formation, EntityManagerInterface $em): JsonResponse
    {
        $user = $this->getUser();
        if (!$user instanceof Instructor && !$user instanceof Student) {
            return $this->json(['message' => 'Utilisateur non autorisé'], 403);
        }

        $formation->removeParticipant($user);
        $em->flush();

        return $this->json(['message' => 'Désinscription réussie']);
    }
}