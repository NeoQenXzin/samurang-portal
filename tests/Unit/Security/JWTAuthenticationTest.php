<?php

namespace App\Tests\Unit\Security;

use DateTime;
use App\Entity\Grade;
use App\Entity\Dojang;
use App\Entity\Instructor;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Lexik\Bundle\JWTAuthenticationBundle\Services\JWTTokenManagerInterface;

class JWTAuthenticationTest extends KernelTestCase
{
    private ?JWTTokenManagerInterface $jwtManager;
    private ?ValidatorInterface $validator;
    
    protected function setUp(): void
    {
        self::bootKernel(); // Initialisation du kernel
        $this->jwtManager = static::getContainer()->get('lexik_jwt_authentication.jwt_manager'); // Initialisation du manager JWT
        $this->validator = static::getContainer()->get('validator'); // Initialisation du validateur
    }

    public function testInvalidInstructorValidation(): void
{
    // Arrange
    $instructor = new Instructor();
    
    // Act
    $violations = $this->validator->validate($instructor);
    
    // Assert
    $this->assertCount(10, $violations);
    
    $violationMessages = [];
    foreach ($violations as $violation) {
        $violationMessages[$violation->getPropertyPath()] = $violation->getMessage();
    }
    
    // Assert avec les messages exacts
    $this->assertArrayHasKey('firstname', $violationMessages);
    $this->assertArrayHasKey('lastname', $violationMessages);
    $this->assertArrayHasKey('birthdate', $violationMessages);
    $this->assertArrayHasKey('adress', $violationMessages);
    $this->assertArrayHasKey('sexe', $violationMessages);
    $this->assertArrayHasKey('mail', $violationMessages);
    $this->assertArrayHasKey('password', $violationMessages);
    $this->assertArrayHasKey('grade', $violationMessages);
    $this->assertArrayHasKey('dojang', $violationMessages);
    $this->assertArrayHasKey('roles', $violationMessages);
    
    // Vérifie les messages exacts
    $this->assertEquals("Le prénom est obligatoire", $violationMessages['firstname']);
    $this->assertEquals("Le nom est obligatoire", $violationMessages['lastname']);
    $this->assertEquals("La date de naissance est obligatoire", $violationMessages['birthdate']);
    $this->assertEquals("L'adresse est obligatoire", $violationMessages['adress']);
    $this->assertEquals("Le sexe est obligatoire", $violationMessages['sexe']);
    $this->assertEquals("Le mail est obligatoire", $violationMessages['mail']);
    $this->assertEquals("Le mot de passe est obligatoire", $violationMessages['password']);
    $this->assertEquals("Le grade est obligatoire", $violationMessages['grade']);
    $this->assertEquals("Le dojang est obligatoire", $violationMessages['dojang']);
    $this->assertEquals("Le rôle est obligatoire", $violationMessages['roles']);
    }

    private function createValidInstructor(): Instructor
    {
        $instructor = new Instructor();

        $instructor->setFirstname('Jonh');
        $instructor->setLastname('Doe');
        $instructor->setMail('test@example.com');
        $instructor->setBirthdate(new DateTime());
        $instructor->setAdress('123 Test Street');
        $instructor->setSexe('M');
        $instructor->setRoles(['ROLE_INSTRUCTOR']);
        $instructor->setPassword('$2y$13$hYMLCWHOqMt7XSMMV.Pw6.HbFZ1pG4r1rcG9YCPxW1Ew0cMTo79Gq');
        
       // Configuration des relations
       $grade = $this->createGrade();
       $dojang = $this->createDojang();
       
       $instructor->setGrade($grade);
       $instructor->setDojang($dojang);

        return $instructor;
    }

    private function createGrade(): Grade
    {
        $grade = new Grade();
        $grade->setName('Black Belt');
        return $grade;
    }

    private function createDojang(): Dojang
    {
        $dojang = new Dojang();
        $dojang->setName('Dojang Test');
        $dojang->setCity('Marseille');
        return $dojang;
    }

    public function testValidInstructorValidation(): void
    {
        // Arrange
        $instructor = $this->createValidInstructor();
        
        // Act
        $violations = $this->validator->validate($instructor);
        
        // Assert
        $this->assertCount(0, $violations, 'L\'instructeur devrait être valide');
    }

    public function testTokenGeneration(): void
    {
        // Arrange
        $instructor = $this->createValidInstructor();
        
        // Act
        $token = $this->jwtManager->create($instructor);
        
        // Assert
        $this->assertIsString($token); // Vérifie que le token est une chaîne de caractères
        $this->assertNotEmpty($token); // Vérifie que le token n'est pas vide
        $this->assertMatchesRegularExpression('/^[\w-]+\.[\w-]+\.[\w-]+$/', $token); // Vérifie que le token est au format JWT
    }

      public function testTokenAuthentication(): void
    {
        // Arrange
        $instructor = $this->createValidInstructor();
        
        // Act
        $token = $this->jwtManager->create($instructor);
        $tokenParts = explode('.', $token);
        $payload = json_decode($this->base64UrlDecode($tokenParts[1]), true);
        
        // Assert
        $this->assertArrayHasKey('exp', $payload, 'Le token devrait avoir une date d\'expiration');
        $this->assertTrue($payload['exp'] > time(), 'Le token ne devrait pas être expiré');
        $this->assertEquals($instructor->getMail(), $payload['username']);
        $this->assertEquals($instructor->getRoles(), $payload['roles']);
    }

    private function base64UrlDecode(string $base64Url): string
    {
        return base64_decode(strtr($base64Url, '-_', '+/') . str_repeat('=', 3 - (3 + strlen($base64Url)) % 4));
    }
}