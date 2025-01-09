<?php

namespace Tests\Integration\Api;

use DateTime;
use App\Entity\Admin;
use App\Entity\Grade;
use App\Entity\Dojang;
use App\Entity\Student;
use App\Entity\Formation;
use App\Entity\NextOrder;
use App\Entity\Instructor;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class ApiEndpointsTest extends WebTestCase
{
    private $client;
    private $entityManager;
    private $instructor;
    private $token;

    protected function setUp(): void
    {
        parent::setUp();
        $this->client = static::createClient();
        $this->entityManager = static::getContainer()->get('doctrine')->getManager();

        // Création des données de test


        // creation d'un grade
        $grade = new Grade();
        $grade->setName('Black Belt');
        $this->entityManager->persist($grade);

        // creation d'un dojang
        $dojang = new Dojang();
        $dojang->setName('Test Dojang');
        $dojang->setCity('Test City');
        $this->entityManager->persist($dojang);

        // creation d'un nextOrder
        $nextOrder = new NextOrder();
        $nextOrder->setStatus('pending');
        $nextOrder->setSendDate(new DateTime());
        $this->entityManager->persist($nextOrder);
        $this->entityManager->flush();

        // Création de l'instructeur test
        $this->instructor = new Instructor();
        $this->instructor->setFirstname('John');
        $this->instructor->setLastname('Doe');
        $this->instructor->setMail('instructor@test.com');
        $this->instructor->setBirthdate(new DateTime());
        $this->instructor->setAdress('123 Test Street');
        $this->instructor->setSexe('M');
        $this->instructor->setPassword(
            static::getContainer()->get('security.password_hasher')
                ->hashPassword($this->instructor, 'test123')
        );
        $this->instructor->setRoles(['ROLE_INSTRUCTOR']);
        $this->instructor->setGrade($grade);
        $this->instructor->setDojang($dojang);

        $this->entityManager->persist($this->instructor);
        $this->entityManager->flush();

        // creation d'un student
        $student = new Student();
        $student->setFirstname('Test');
        $student->setLastname('Student');
        $student->setMail('student@test.com');
        $student->setBirthdate(new DateTime());
        $student->setAdress('Test Address');
        $student->setSexe('M');
        $student->setPassword(
            static::getContainer()->get('security.password_hasher')
                ->hashPassword($student, 'password123')
        );
        $student->setGrade($grade);
        $student->setDojang($dojang);
        $student->setInstructor($this->instructor);
        $student->setRoles(['ROLE_STUDENT']);
        $this->entityManager->persist($student);
        $this->entityManager->flush();

        // creation d'une formation
        $formation = new Formation();
        $formation->setType('Stage');
        $formation->setDescription('Test Formation');
        $formation->setStartDate(new DateTime());
        $formation->setEndDate(new DateTime());
        $formation->setLocation('Test Location');
        $formation->setOrganizer($this->instructor);
        $this->entityManager->persist($formation);
        $this->entityManager->flush();


        // Obtention du token JWT
        $this->client->request(
            'POST',
            '/api/login_check',
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
            json_encode([
                'username' => 'instructor@test.com',
                'password' => 'test123'
            ])
        );

        $data = json_decode($this->client->getResponse()->getContent(), true);
        $this->token = $data['token'];
    }

    // private function getToken(string $username, string $password): string
    // {
    //     $this->client->request(
    //         'POST',
    //         '/api/login_check',
    //         [],
    //         [],
    //         ['CONTENT_TYPE' => 'application/json'],
    //         json_encode([
    //             'username' => $username,
    //             'password' => $password
    //         ])
    //     );
    //     $data = json_decode($this->client->getResponse()->getContent(), true);
    //     return $data['token'];
    // }

    public function testInstructorEndpoints(): void
    {
        // GET collection
        $this->client->request('GET', '/api/instructors', [], [], [
            'HTTP_AUTHORIZATION' => 'Bearer ' . $this->token,
            'CONTENT_TYPE' => 'application/ld+json'
        ]);
        $this->assertResponseIsSuccessful();

        // GET item
        $this->client->request('GET', '/api/instructors/' . $this->instructor->getId(), [], [], [
            'HTTP_AUTHORIZATION' => 'Bearer ' . $this->token,
            'CONTENT_TYPE' => 'application/ld+json'
        ]);
        $this->assertResponseIsSuccessful();

        // PATCH update
        $updateData = [
            'firstname' => 'Updated Name'
        ];
        $this->client->request('PATCH', '/api/instructors/' . $this->instructor->getId(), [], [], [
            'HTTP_AUTHORIZATION' => 'Bearer ' . $this->token,
            'CONTENT_TYPE' => 'application/merge-patch+json'
        ], json_encode($updateData));
        $this->assertResponseIsSuccessful();
    }

    public function testStudentEndpoints(): void
    {
        // GET collection
        $this->client->request('GET', '/api/students', [], [], [
            'HTTP_AUTHORIZATION' => 'Bearer ' . $this->token,
            'CONTENT_TYPE' => 'application/ld+json'
        ]);
        $this->assertResponseIsSuccessful();


        // GET collection
        $this->client->request('GET', '/api/students', [], [], [
            'HTTP_AUTHORIZATION' => 'Bearer ' . $this->token,
            'CONTENT_TYPE' => 'application/ld+json'
        ]);
        $this->assertResponseIsSuccessful();

        // GET item
        $student = $this->entityManager->getRepository(Student::class)->findOneBy([]);
        $this->client->request('GET', '/api/students/' . $student->getId(), [], [], [
            'HTTP_AUTHORIZATION' => 'Bearer ' . $this->token,
            'CONTENT_TYPE' => 'application/ld+json'
        ]);
        $this->assertResponseIsSuccessful();

        // PATCH update
        $updateData = [
            'firstname' => 'Updated Student Name'
        ];
        $this->client->request('PATCH', '/api/students/' . $student->getId(), [], [], [
            'HTTP_AUTHORIZATION' => 'Bearer ' . $this->token,
            'CONTENT_TYPE' => 'application/merge-patch+json'
        ], json_encode($updateData));
        $this->assertResponseIsSuccessful();
    }

    public function testNextOrderEndpoints(): void
{
    // GET collection
    $this->client->request('GET', '/api/next_orders', [], [], [
        'HTTP_AUTHORIZATION' => 'Bearer ' . $this->token,
        'CONTENT_TYPE' => 'application/ld+json'
    ]);
    $this->assertResponseIsSuccessful();
}


    public function testGetFormations(): void
    {
        // Test GET collection
        $this->client->request('GET', '/api/formations', [], [], [
            'HTTP_AUTHORIZATION' => 'Bearer ' . $this->token,
            'CONTENT_TYPE' => 'application/json'
        ]);

        $this->assertResponseIsSuccessful();
        $this->assertResponseHeaderSame('content-type', 'application/ld+json; charset=utf-8');
    }

    public function testCreateFormation(): void
    {
        $formationData = [
            'type' => 'Stage',
            'description' => 'Test Formation',
            'startDate' => '2024-03-20T10:00:00+00:00',
            'endDate' => '2024-03-20T12:00:00+00:00',
            'location' => 'Test Location',
            'organizer' => '/api/instructors/' . $this->instructor->getId()
        ];

        $this->client->request('POST', '/api/formations', [], [], [
            'HTTP_AUTHORIZATION' => 'Bearer ' . $this->token,
            'CONTENT_TYPE' => 'application/ld+json'
        ], json_encode($formationData));

        $this->assertResponseStatusCodeSame(Response::HTTP_CREATED);
        $this->assertResponseHeaderSame('content-type', 'application/ld+json; charset=utf-8');

        $responseData = json_decode($this->client->getResponse()->getContent(), true);
        $this->assertArrayHasKey('id', $responseData);
        $this->assertEquals('Stage', $responseData['type']);
    }

    public function testUpdateFormation(): void
    {
        // Création d'une formation
        $formation = new Formation();
        $formation->setType('Stage');
        $formation->setDescription('Initial Description');
        $formation->setStartDate(new DateTime('2024-03-20T10:00:00+00:00'));
        $formation->setEndDate(new DateTime('2024-03-20T12:00:00+00:00'));
        $formation->setLocation('Initial Location');
        $formation->setOrganizer($this->instructor);

        $this->entityManager->persist($formation);
        $this->entityManager->flush();

        // Mise à jour de la formation
        $updateData = [
            'type' => 'Updated Type',
            'organizer' => '/api/instructors/' . $this->instructor->getId(),
            'description' => 'Updated Description',
            'location' => 'Updated Location',
            'startDate' => '2024-03-20T10:00:00+00:00',
            'endDate' => '2024-03-20T12:00:00+00:00'
        ];
        

        $this->client->request('PUT', '/api/formations/'.$formation->getId(), [], [], [
            'HTTP_AUTHORIZATION' => 'Bearer ' . $this->token,
            'CONTENT_TYPE' => 'application/ld+json'
        ], json_encode($updateData));

        $this->assertResponseIsSuccessful();

        $responseData = json_decode($this->client->getResponse()->getContent(), true);
        $this->assertEquals('Updated Description', $responseData['description']);
        $this->assertEquals('Updated Location', $responseData['location']);
    }

    public function testDeleteFormation(): void
    {
        // Création d'une formation
        $formation = new Formation();
        $formation->setType('Stage');
        $formation->setDescription('Test Description');
        $formation->setStartDate(new DateTime('2024-03-20T10:00:00+00:00'));
        $formation->setEndDate(new DateTime('2024-03-20T12:00:00+00:00'));
        $formation->setLocation('Test Location');
        $formation->setOrganizer($this->instructor);

        $this->entityManager->persist($formation);
        $this->entityManager->flush();

        // Suppression de la formation
        $this->client->request('DELETE', '/api/formations/' . $formation->getId(), [], [], [
            'HTTP_AUTHORIZATION' => 'Bearer ' . $this->token
        ]);

        $this->assertResponseStatusCodeSame(Response::HTTP_NO_CONTENT);
    }


    public function testGetDojangAndGrade(): void
    {
        // Test GET collection pour Dojang
        $this->client->request('GET', '/api/dojangs', [], [], [
            'HTTP_AUTHORIZATION' => 'Bearer ' . $this->token
        ]);
        $this->assertResponseIsSuccessful();

        // Test GET collection pour Grade
        $this->client->request('GET', '/api/grades', [], [], [
            'HTTP_AUTHORIZATION' => 'Bearer ' . $this->token
        ]);
        $this->assertResponseIsSuccessful();
    }

    protected function tearDown(): void
    {
        parent::tearDown();

        if ($this->entityManager) {
            $this->entityManager->createQuery('DELETE FROM App\Entity\NextOrder')->execute();
            $this->entityManager->createQuery('DELETE FROM App\Entity\Formation')->execute();
            $this->entityManager->createQuery('DELETE FROM App\Entity\Student')->execute();
            $this->entityManager->createQuery('DELETE FROM App\Entity\Instructor')->execute();
            $this->entityManager->createQuery('DELETE FROM App\Entity\Grade')->execute();
            $this->entityManager->createQuery('DELETE FROM App\Entity\Dojang')->execute();

            $this->entityManager->close();
            $this->entityManager = null;
        }
    }
}
