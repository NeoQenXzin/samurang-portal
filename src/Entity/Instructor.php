<?php

namespace App\Entity;


use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use ApiPlatform\Metadata\ApiResource;
use App\Repository\InstructorRepository;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;

#[ApiResource]
#[ORM\Entity(repositoryClass: InstructorRepository::class)]
class Instructor implements UserInterface, PasswordAuthenticatedUserInterface, \Stringable
{
    
    public function __toString(): string
{
    return $this->getFirstName() . ' ' . $this->getLastName() . ' (' . $this->getMail() . ')';
}

#[ORM\Id]
#[ORM\GeneratedValue]
#[ORM\Column]
private ?int $id = null;

#[ORM\Column(length: 70)]
private ?string $firstname = null;

#[ORM\Column(length: 70)]
private ?string $lastname = null;

#[ORM\Column(type: Types::DATETIME_MUTABLE)]
private ?\DateTimeInterface $birthdate = null;

#[ORM\Column(length: 255)]
private ?string $adress = null;

#[ORM\Column(length: 25)]
private ?string $sexe = null;

#[ORM\Column(length: 25, nullable: true)]
private ?string $tel = null;

#[ORM\Column(length: 70)]
private ?string $mail = null;


#[ORM\Column(length: 25, nullable: true)]
private ?string $passport = null;

#[ORM\ManyToOne(inversedBy: 'instructors')]
#[ORM\JoinColumn(nullable: false)]
private ?Grade $grade = null;

#[ORM\ManyToOne(inversedBy: 'instructors')]
private ?Dojang $dojang = null;

public function getId(): ?int
{
    return $this->id;
}

public function getFirstname(): ?string
{
    return $this->firstname;
}

public function setFirstname(string $firstname): static
{
    $this->firstname = $firstname;

    return $this;
}

public function getLastname(): ?string
{
    return $this->lastname;
}

public function setLastname(string $lastname): static
{
    $this->lastname = $lastname;

    return $this;
}

public function getBirthdate(): ?\DateTimeInterface
{
    return $this->birthdate;
}

public function setBirthdate(\DateTimeInterface $birthdate): static
{
    $this->birthdate = $birthdate;

    return $this;
}

public function getAdress(): ?string
{
    return $this->adress;
}

public function setAdress(string $adress): static
{
    $this->adress = $adress;

    return $this;
}

public function getSexe(): ?string
{
    return $this->sexe;
}

public function setSexe(string $sexe): static
{
    $this->sexe = $sexe;

    return $this;
}

public function getTel(): ?string
{
    return $this->tel;
}

public function setTel(?string $tel): static
{
    $this->tel = $tel;

    return $this;
}

public function getMail(): ?string
{
    return $this->mail;
}

public function setMail(string $mail): static
{
    $this->mail = $mail;

    return $this;
}

public function getPassport(): ?string
{
    return $this->passport;
}

public function setPassport(?string $passport): static
{
    $this->passport = $passport;

    return $this;
}

public function getGrade(): ?Grade
{
    return $this->grade;
}

public function setGrade(?Grade $grade): static
{
    $this->grade = $grade;

    return $this;
}

public function getDojang(): ?Dojang
{
    return $this->dojang;
}

public function setDojang(?Dojang $dojang): static
{
    $this->dojang = $dojang;

    return $this;
}
    /**
     * @var Collection<int, Student>
     */
    #[ORM\OneToMany(targetEntity: Student::class, mappedBy: 'instructor')]
    private Collection $students;

    public function __construct()
    {
        $this->students = new ArrayCollection();
    }

    /**
     * @return Collection<int, Student>
     */
    public function getStudents(): Collection
    {
        return $this->students;
    }

    public function addStudent(Student $student): static
    {
        if (!$this->students->contains($student)) {
            $this->students->add($student);
            $student->setInstructor($this);
        }

        return $this;
    }

    public function removeStudent(Student $student): static
    {
        if ($this->students->removeElement($student)) {
            // set the owning side to null (unless already changed)
            if ($student->getInstructor() === $this) {
                $student->setInstructor(null);
            }
        }

        return $this;
    }


    /**
     * @var list<string> The user roles
     */
    #[ORM\Column]
    private array $roles = [];

    /**
     * @var string The hashed password
     */
    #[ORM\Column]
    private ?string $password = null;

 

    /**
     * A visual identifier that represents this user.
     *
     * @see UserInterface
     */
    public function getUserIdentifier(): string
    {
        return (string) $this->getMail();
    }

    /**
     * @see UserInterface
     * @return list<string>
     */
    public function getRoles(): array
    {
        $roles = $this->roles;
        // guarantee every user at least has ROLE_USER
        $roles[] = 'ROLE_INSTRUCTOR';

        return array_unique($roles);
    }

    /**
     * @param list<string> $roles
     */
    public function setRoles(array $roles): static
    {
        $this->roles = $roles;

        return $this;
    }

    /**
     * @see PasswordAuthenticatedUserInterface
     */
    public function getPassword(): ?string
    {
        return $this->password;
    }

    public function setPassword(string $password): static
    {
        $this->password = $password;

        return $this;
    }

    /**
     * @see UserInterface
     */
    public function eraseCredentials(): void
    {
        // If you store any temporary, sensitive data on the user, clear it here
        // $this->plainPassword = null;
    }
}
