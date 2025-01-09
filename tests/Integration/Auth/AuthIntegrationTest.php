<?php

namespace App\Tests\Integration;

use DateTime;
use App\Entity\Grade;
use App\Entity\Dojang;
use App\Entity\Instructor;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Lexik\Bundle\JWTAuthenticationBundle\Services\JWTTokenManagerInterface;

class AuthenticationIntegrationTest extends WebTestCase
{
    private $client;
    private $entityManager;
    private $instructor;
    private $jwtManager;

    protected function setUp(): void
    {
        parent::setUp();
        $this->client = static::createClient();
        $this->entityManager = static::getContainer()->get('doctrine')->getManager();
        $this->jwtManager = static::getContainer()->get('lexik_jwt_authentication.jwt_manager');

        // Création des données de test
        $grade = new Grade();
        $grade->setName('Black Belt');
        $this->entityManager->persist($grade);

        $dojang = new Dojang();
        $dojang->setName('Test Dojang');
        $dojang->setCity('Test City');
        $this->entityManager->persist($dojang);

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
    }

    public function testInstructorAuthentication(): void
    {
        // Génération du token JWT
        $token = $this->jwtManager->create($this->instructor);

        // Vérification du token
        $this->assertIsString($token);
        $this->assertNotEmpty($token);
        $this->assertMatchesRegularExpression('/^[\w-]+\.[\w-]+\.[\w-]+$/', $token);

        // Test de l'API login_check
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

        $response = $this->client->getResponse();
        $this->assertResponseIsSuccessful();
        $data = json_decode($response->getContent(), true);
        $this->assertArrayHasKey('token', $data);

        // Test d'accès à une route protégée avec le token
        $this->client->request(
            'GET',
            '/api/instructors',
            [],
            [],
            [
                'HTTP_Authorization' => 'Bearer ' . $token,
                'CONTENT_TYPE' => 'application/json'
            ]
        );

        $this->assertResponseIsSuccessful();
    }

    public function testInvalidAuthentication(): void
    {
        $this->client->request(
            'POST',
            '/api/login_check',
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
            json_encode([
                'username' => 'wrong@email.com',
                'password' => 'wrongpassword'
            ])
        );

        $this->assertResponseStatusCodeSame(401);
    }

    public function testTokenPayload(): void
    {
        $token = $this->jwtManager->create($this->instructor);
        $tokenParts = explode('.', $token);
        $payload = json_decode(base64_decode($tokenParts[1]), true);

        $this->assertEquals($this->instructor->getMail(), $payload['username']);
        $this->assertTrue(in_array('ROLE_INSTRUCTOR', $payload['roles']));
    }

    public function testLogout(): void
    {
        $token = $this->jwtManager->create($this->instructor);

        // Test de déconnexion via l'API
        $this->client->request(
            'POST',
            '/api/logout',
            [],
            [],
            [
                'HTTP_Authorization' => 'Bearer ' . $token,
                'CONTENT_TYPE' => 'application/json'
            ]
        );

        $this->assertResponseIsSuccessful();
        $responseData = json_decode($this->client->getResponse()->getContent(), true);
        $this->assertEquals('Déconnexion réussie', $responseData['message']);

    }


    protected function tearDown(): void
    {
        parent::tearDown();

        // Nettoyage de la base de données
        if ($this->entityManager) {
            $this->entityManager->createQuery('DELETE FROM App\Entity\Instructor')->execute();
            $this->entityManager->createQuery('DELETE FROM App\Entity\Grade')->execute();
            $this->entityManager->createQuery('DELETE FROM App\Entity\Dojang')->execute();
            // $this->entityManager->createQuery('DELETE FROM App\Entity\Formation')->execute();

            $this->entityManager->close();
            $this->entityManager = null;
        }
    }
}