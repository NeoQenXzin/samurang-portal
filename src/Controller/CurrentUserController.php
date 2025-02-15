<?php
namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\User\UserInterface;
use App\Entity\Admin;
use App\Entity\Instructor;
use App\Entity\Student;
use Doctrine\ORM\EntityManagerInterface;

class CurrentUserController extends AbstractController
{
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    #[Route('/api/current_user', name: 'api_current_user', methods: ['GET'])]
    public function getCurrentUser(): JsonResponse
    {
        try {
            $user = $this->getUser();
            if (!$user instanceof UserInterface) {
                return new JsonResponse(['error' => 'User not found'], 404);
            }

            $userData = [
                'userIdentifier' => $user->getUserIdentifier(),
                'roles' => $user->getRoles(),
            ];

            if ($user instanceof Admin) {
                $userData = $this->getAdminData($user, $userData);
            } elseif ($user instanceof Instructor) {
                $userData = $this->getInstructorData($user, $userData);
            } elseif ($user instanceof Student) {
                $userData = $this->getStudentData($user, $userData);
            }

            return $this->json($userData);
        } catch (\Exception $e) {
            return new JsonResponse([
                'error' => 'Une erreur est survenue',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    private function getAdminData(Admin $user, array $userData): array
    {
        $userData['type'] = 'Admin';
        $userData['id'] = $user->getId();
        $userData['email'] = $user->getEmail();
        return $userData;
    }

    private function getInstructorData(Instructor $user, array $userData): array
    {
        $userData['type'] = 'Instructor';
        $userData['id'] = $user->getId();
        $userData['firstname'] = $user->getFirstname();
        $userData['lastname'] = $user->getLastname();
        $userData['mail'] = $user->getMail();

        if ($dojang = $user->getDojang()) {
            $userData['dojang'] = [
                'id' => $dojang->getId(),
                'name' => $dojang->getName(),
            ];
        }

        if ($grade = $user->getGrade()) {
            $userData['grade'] = [
                'id' => $grade->getId(),
                'name' => $grade->getName(),
            ];
        }

        // Chargement explicite des étudiants
        $students = $this->entityManager
            ->getRepository(Student::class)
            ->findBy(['instructor' => $user]);
            
        $userData['studentsCount'] = count($students);

        return $userData;
    }

    private function getStudentData(Student $user, array $userData): array
    {
        $userData['type'] = 'Student';
        $userData['id'] = $user->getId();
        $userData['firstname'] = $user->getFirstname();
        $userData['lastname'] = $user->getLastname();
        $userData['mail'] = $user->getMail();
        $userData['sexe'] = $user->getSexe();
        $userData['adress'] = $user->getAdress();
        $userData['passport'] = $user->getPassport();
        $userData['tel'] = $user->getTel();
        $userData['birthdate'] = $user->getBirthdate()?->format('Y-m-d');

        if ($dojang = $user->getDojang()) {
            $userData['dojang'] = [
                'id' => $dojang->getId(),
                'name' => $dojang->getName(),
            ];
        }

        if ($grade = $user->getGrade()) {
            $userData['grade'] = [
                'id' => $grade->getId(),
                'name' => $grade->getName(),
            ];
        }

        return $userData;
    }
}