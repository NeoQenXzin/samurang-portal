<?php

namespace Tests\Integration\EasyAdmin;

use DateTime;
use App\Entity\Grade;
use App\Entity\Dojang;
use App\Entity\Student;
use App\Entity\Formation;
use App\Entity\Instructor;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;

class InstructorDashboardTest extends WebTestCase
{
    private $client;
    private $entityManager;
    private $instructor;
    private $dojang;
    private $grade;

    protected function setUp(): void
    {
        parent::setUp();
        $this->client = static::createClient();
        $this->entityManager = static::getContainer()->get('doctrine')->getManager();

        // Création des données de test
        $this->grade = new Grade();
        $this->grade->setName('Black Belt');
        $this->entityManager->persist($this->grade);

        $this->dojang = new Dojang();
        $this->dojang->setName('Test Dojang');
        $this->dojang->setCity('Test City');
        $this->entityManager->persist($this->dojang);

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
        $this->instructor->setGrade($this->grade);
        $this->instructor->setDojang($this->dojang);

        $this->entityManager->persist($this->instructor);
        $this->entityManager->flush();
    }

    public function testDashboardAccessAndContent(): void
    {
        $this->loginAsInstructor();

        // Test d'accès au dashboard
        $crawler = $this->client->request('GET', '/mydojang');
        
        // Vérifications du contenu
        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextContains('.content-header-title', 'Welcome to Your Dojang');
        // $this->assertSelectorExists('a[href*="/mydojang/student"]');
        // $this->assertSelectorExists('a[href*="/mydojang/formation"]');
    }

    // public function testStudentManagement(): void
    // {
    //     $this->loginAsInstructor();

    //     // Test liste des étudiants
    //     $crawler = $this->client->request('GET', '/mydojang/student');
    //     $this->assertResponseIsSuccessful();
    //     $this->assertSelectorTextContains('.content-header-title', 'My Students');

    //     // Test création d'étudiant
    //     $crawler = $this->client->request('GET', '/mydojang/student/new');
    //     $this->assertResponseIsSuccessful();
        
    //     // Vérification des champs du formulaire
    //     $this->assertSelectorExists('input[name="Student[firstName]"]');
    //     $this->assertSelectorExists('input[name="Student[lastName]"]');
    //     $this->assertSelectorExists('input[name="Student[mail]"]');
    //     $this->assertSelectorExists('select[name="Student[grade]"]');
    // }

    // public function testFormationManagement(): void
    // {
    //     $this->loginAsInstructor();

    //     // Test liste des formations
    //     $crawler = $this->client->request('GET', '/mydojang/formation');
    //     $this->assertResponseIsSuccessful();
    //     $this->assertSelectorTextContains('.content-header-title', 'My Formations');

    //     // Test création de formation
    //     $crawler = $this->client->request('GET', '/mydojang/formation/new');
    //     $this->assertResponseIsSuccessful();
        
    //     // Vérification des champs du formulaire
    //     $this->assertSelectorExists('select[name="Formation[type]"]');
    //     $this->assertSelectorExists('textarea[name="Formation[description]"]');
    //     $this->assertSelectorExists('input[name="Formation[location]"]');
    // }

    public function testSecurityRestrictions(): void
    {
        // Test accès sans authentification
        $this->client->request('GET', '/mydojang');
        $this->assertResponseRedirects('/login');

        // Test accès avec authentification
        $this->loginAsInstructor();
        
        // Test accès au dashboard admin (doit être refusé)
        $this->client->request('GET', '/admin');
        $this->assertResponseStatusCodeSame(Response::HTTP_FORBIDDEN);

        // Test accès aux étudiants d'un autre instructeur
        // $otherStudent = new Student();
        // $otherStudent->setFirstname('Other');
        // $otherStudent->setLastname('Student');
        // $otherStudent->setMail('other@test.com');
        // $otherStudent->setBirthdate(new DateTime());
        // $otherStudent->setAdress('456 Other Street');
        // $otherStudent->setSexe('F');
        // $otherStudent->setPassword('password');
        // $otherStudent->setGrade($this->grade);
        // $otherStudent->setDojang($this->dojang);
        
        // $this->entityManager->persist($otherStudent);
        // $this->entityManager->flush();

        // $crawler = $this->client->request('GET', '/mydojang/student');
        // $this->assertResponseIsSuccessful();
        // $this->assertSelectorNotExists('td:contains("Other Student")');
    }

    private function loginAsInstructor(): void
    {
        $this->client->request('GET', '/login');
        $this->client->submitForm('Se connecter', [
            '_username' => 'instructor@test.com',
            '_password' => 'test123',
        ]);
        $this->client->followRedirect();
        
    }

    protected function tearDown(): void
    {
        parent::tearDown();

        if ($this->entityManager) {
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