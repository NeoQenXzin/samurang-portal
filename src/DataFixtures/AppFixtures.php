<?php

namespace App\DataFixtures;

use App\Entity\Grade;
use App\Entity\Dojang;
use App\Entity\Student;
use App\Entity\Formation;
use App\Entity\Instructor;
use App\Entity\Admin;
use App\Entity\NextOrder;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class AppFixtures extends Fixture
{
    private $passwordHasher;

    public function __construct(UserPasswordHasherInterface $passwordHasher)
    {
        $this->passwordHasher = $passwordHasher;
    }

    public function load(ObjectManager $manager)
    {
        // Création de l'admin
        $admin = new Admin();
        $admin->setEmail('admin@samurang.com');
        $hashedPassword = $this->passwordHasher->hashPassword($admin, 'password123');
        $admin->setPassword($hashedPassword);
        $admin->setRoles(['ROLE_ADMIN']);
        $manager->persist($admin);

        // Création d'une commande initiale (NextOrder)
        $nextOrder = new NextOrder();
        $nextOrder->setStatus('Arrivée');
        $nextOrder->setSendDate(new \DateTime('2025-01-01'));;
        $nextOrder->setReceiveDate(new \DateTime('2025-02-02'));;
        $manager->persist($nextOrder);

        // Création des grades
        $grades = [
            '10ème Keup' => new Grade(),
            '5ème Keup' => new Grade(),
            '1er Keup' => new Grade(),
            '1er Dan' => new Grade(),
            '2ème Dan' => new Grade(),
            '3ème Dan' => new Grade(),
            '4ème Dan' => new Grade(),
            '5ème Dan' => new Grade(),
        ];

        foreach ($grades as $name => $grade) {
            $grade->setName($name);
            $manager->persist($grade);
        }

        // Création des dojangs
        $dojangs = [
            ['name' => 'Dojang Central Paris', 'city' => 'Paris'],
            ['name' => 'Dojang Lyon Centre', 'city' => 'Lyon'],
            ['name' => 'Dojang Marseille', 'city' => 'Marseille'],
            ['name' => 'Dojang Bordeaux', 'city' => 'Bordeaux'],
        ];

        $dojangEntities = [];
        foreach ($dojangs as $dojangData) {
            $dojang = new Dojang();
            $dojang->setName($dojangData['name']);
            $dojang->setCity($dojangData['city']);
            $dojangEntities[] = $dojang;
            $manager->persist($dojang);
        }

        // Création des instructeurs
        $instructors = [
            [
                'firstname' => 'Chan-woo',
                'lastname' => 'Park',
                'mail' => 'park@test.com',
                'grade' => $grades['5ème Dan'],
                'dojang' => $dojangEntities[0],
                // Ajout des étudiants pour l'instructeur
                'students' => [
                    [
                        'firstname' => 'Lucas',
                        'lastname' => 'Bernard',
                        'grade' => $grades['1er Dan']
                    ],
                    [
                        'firstname' => 'Emma',
                        'lastname' => 'Petit',
                        'grade' => $grades['1er Keup']
                    ]
                ]
            ],
            [
                'firstname' => 'Marie',
                'lastname' => 'Martin',
                'mail' => 'martin@test.com',
                'grade' => $grades['4ème Dan'],
                'dojang' => $dojangEntities[0],
                // Ajout des étudiants pour l'instructeur
                'students' => [
                    [
                        'firstname' => 'Louis',
                        'lastname' => 'Dubois',
                        'grade' => $grades['2ème Dan']
                    ]
                ]
            ],
            [
                'firstname' => 'Jean',
                'lastname' => 'Dupont',
                'mail' => 'dupont@test.com',
                'grade' => $grades['3ème Dan'],
                'dojang' => $dojangEntities[1],
                // Ajout des étudiants pour l'instructeur
                'students' => [
                    [
                        'firstname' => 'Jade',
                        'lastname' => 'Moreau',
                        'grade' => $grades['5ème Keup']
                    ]
                ]
            ],
            [
                'firstname' => 'Sophie',
                'lastname' => 'Laurent',
                'mail' => 'laurent@test.com',
                'grade' => $grades['3ème Dan'],
                'dojang' => $dojangEntities[2],
                // Ajout des étudiants pour l'instructeur
                'students' => [
                    [
                        'firstname' => 'Gabriel',
                        'lastname' => 'Roux',
                        'grade' => $grades['10ème Keup']
                    ]
                ]
            ],
        ];

        $instructorEntities = [];
        foreach ($instructors as $instructorData) {
            $instructor = new Instructor();
            $instructor->setFirstname($instructorData['firstname']);
            $instructor->setLastname($instructorData['lastname']);
            $instructor->setMail($instructorData['mail']);
            $hashedPassword = $this->passwordHasher->hashPassword($instructor, 'password123');
            $instructor->setPassword($hashedPassword);
            $instructor->setBirthdate(new \DateTime('1980-01-01'));
            $instructor->setAdress('123 rue ' . $instructorData['dojang']->getCity());
            $instructor->setSexe(random_int(0, 1) ? 'M' : 'F');
            $instructor->setGrade($instructorData['grade']);
            $instructor->setDojang($instructorData['dojang']);
            $instructor->setRoles(['ROLE_INSTRUCTOR']);

            // Création des étudiants pour cet instructeur
            foreach ($instructorData['students'] as $studentData) {
                $student = new Student();
                $student->setFirstname($studentData['firstname']);
                $student->setLastname($studentData['lastname']);
                $student->setMail(strtolower($studentData['firstname']) . '.' . strtolower($studentData['lastname']) . '@test.com');
                $hashedPassword = $this->passwordHasher->hashPassword($student, 'password123');
                $student->setPassword($hashedPassword);
                $student->setBirthdate(new \DateTime('2000-01-01'));
                $student->setAdress('456 rue ' . $instructorData['dojang']->getCity());
                $student->setSexe(random_int(0, 1) ? 'M' : 'F');
                $student->setGrade($studentData['grade']);
                $student->setDojang($instructorData['dojang']);
                $student->setRoles(['ROLE_STUDENT']);
                $student->setInstructor($instructor); // Lien avec l'instructeur

                $manager->persist($student);
            }

            $instructorEntities[] = $instructor;
            $manager->persist($instructor);
        }

        // Création des formations
        $formations = [
            [
                'type' => 'Stage technique',
                'description' => 'Stage de perfectionnement technique tous niveaux',
                'startDate' => '+2 weeks',
                'duration' => '+2 days',
                'organizer' => $instructorEntities[0],
                'location' => $dojangEntities[0]->getName()
            ],
            [
                'type' => 'Préparation Dan',
                'description' => 'Préparation aux passages de grade Dan',
                'startDate' => '+1 month',
                'duration' => '+1 day',
                'organizer' => $instructorEntities[1],
                'location' => $dojangEntities[0]->getName()
            ],
            [
                'type' => 'Stage Combat',
                'description' => 'Stage spécial combat et self-défense',
                'startDate' => '+3 weeks',
                'duration' => '+1 day',
                'organizer' => $instructorEntities[2],
                'location' => $dojangEntities[1]->getName()
            ],
        ];

        foreach ($formations as $formationData) {
            $formation = new Formation();
            $formation->setType($formationData['type']);
            $formation->setDescription($formationData['description']);
            $formation->setStartDate(new \DateTime($formationData['startDate']));
            $formation->setEndDate(new \DateTime($formationData['startDate'] . ' ' . $formationData['duration']));
            $formation->setLocation($formationData['location']);
            $formation->setOrganizer($formationData['organizer']);
            $manager->persist($formation);
        }

        $manager->flush();
    }
}
