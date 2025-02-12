<?php
namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\User\UserInterface;
use App\Entity\Admin;
use App\Entity\Instructor;
use App\Entity\Student;

class CurrentUserController extends AbstractController
{
    #[Route('/api/current_user', name: 'api_current_user', methods: ['GET'])]
    public function getCurrentUser(): JsonResponse
    {
        $user = $this->getUser();
        if (!$user instanceof UserInterface) {
            return new JsonResponse(['error' => 'User not found'], 404);
        }

        $userData = [
            'userIdentifier' => $user->getUserIdentifier(),
            'roles' => $user->getRoles(),
        ];

        // Ajout conditionnel des informations spécifiques à chaque type d'utilisateur
        if ($user instanceof Admin) {
            $userData['type'] = 'Admin';
            $userData['id'] = $user->getId();
            $userData['email'] = $user->getEmail();
        } elseif ($user instanceof Instructor) {
            $userData['type'] = 'Instructor';
            $userData['id'] = $user->getId();
            $userData['firstname'] = $user->getFirstname();
            $userData['lastname'] = $user->getLastname();
            $userData['mail'] = $user->getMail();
            if ($user->getDojang()) {
                $userData['dojang'] = [
                    'id' => $user->getDojang()->getId(),
                    'name' => $user->getDojang()->getName(),
                ];
            }
            if ($user->getGrade()) {
                $userData['grade'] = [
                    'id' => $user->getGrade()->getId(),
                    'name' => $user->getGrade()->getName(),
                ];
            }
            $userData['studentsCount'] = count($user->getStudents());
        } elseif ($user instanceof Student) {
            $userData['type'] = 'Student';
            $userData['id'] = $user->getId();
            $userData['firstname'] = $user->getFirstname();
            $userData['lastname'] = $user->getLastname();
            $userData['mail'] = $user->getMail();
            $userData['sexe'] = $user->getSexe();
            $userData['adress'] = $user->getAdress();
            $userData['passport'] = $user->getPassport();
            $userData['tel'] = $user->getTel();
            $userData['birthdate'] = $user->getBirthdate();
            if ($user->getDojang()) {
                $userData['dojang'] = [
                    'id' => $user->getDojang()->getId(),
                    'name' => $user->getDojang()->getName(),
                ];
            }
            if ($user->getGrade()) {
                $userData['grade'] = [
                    'id' => $user->getGrade()->getId(),
                    'name' => $user->getGrade()->getName(),
                ];
            }
        }

        return $this->json($userData);
    }
}