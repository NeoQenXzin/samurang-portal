<?php

namespace App\Entity;


use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use ApiPlatform\Metadata\ApiResource;
use App\Repository\InstructorRepository;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Validator\Constraints as Assert;


#[ApiResource(
    normalizationContext: ['groups' => ['instructor:read']],
    denormalizationContext: ['groups' => ['instructor:write']]
)]
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
    #[Groups(['instructor:write', 'formation:read'])]
    private ?int $id = null;

    #[Assert\NotBlank(message: "Le prénom est obligatoire")]
    #[ORM\Column(length: 70)]
    #[Groups(['instructor:read', 'instructor:write', 'formation:read'])]
    private ?string $firstname = null;

    #[Assert\NotBlank(message: "Le nom est obligatoire")]
    #[ORM\Column(length: 70)]
    #[Groups(['instructor:read', 'instructor:write', 'formation:read'])]
    private ?string $lastname = null;

    #[Assert\NotBlank(message: "La date de naissance est obligatoire")]
    #[Groups(['instructor:read', 'instructor:write'])]
    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $birthdate = null;

    #[Assert\NotBlank(message: "L'adresse est obligatoire")]
    #[Groups(['instructor:read', 'instructor:write'])]
    #[ORM\Column(length: 255)]
    private ?string $adress = null;

    #[Assert\NotBlank(message: "Le sexe est obligatoire")]
    #[Groups(['instructor:read', 'instructor:write'])]
    #[ORM\Column(length: 25)]
    private ?string $sexe = null;

    #[Groups(['instructor:read', 'instructor:write'])]
    #[ORM\Column(length: 25, nullable: true)]
    private ?string $tel = null;

    #[Assert\NotBlank(message: "Le mail est obligatoire")]
    #[Groups(['instructor:read', 'instructor:write'])]
    #[ORM\Column(length: 70)]
    private ?string $mail = null;

    #[Groups(['instructor:read', 'instructor:write'])]
    #[ORM\Column(length: 25, nullable: true)]
    private ?string $passport = null;

    #[Assert\NotBlank(message: "Le grade est obligatoire")]
    #[Groups(['instructor:read', 'instructor:write'])]
    #[ORM\ManyToOne(inversedBy: 'instructors') ]
    #[ORM\JoinColumn(nullable: false)]
    private ?Grade $grade = null;

    #[Assert\NotBlank(message: "Le dojang est obligatoire")]
    #[Groups(['instructor:read', 'instructor:write'])]
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
    #[Groups(['instructor:read'])]
    #[ORM\OneToMany(targetEntity: Student::class, mappedBy: 'instructor')]
    private Collection $students;

    public function __construct()
    {
        $this->students = new ArrayCollection();
        $this->organizedFormations = new ArrayCollection();
        $this->participatedFormations = new ArrayCollection();
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
    #[Assert\NotBlank(message: "Le rôle est obligatoire")]
    #[ORM\Column]
    private array $roles = [];

    /**
     * @var string The hashed password
     */
    #[Assert\NotBlank(message: "Le mot de passe est obligatoire")]
    #[ORM\Column]
    private ?string $password = null;

    /**
     * @var Collection<int, Formation>
     */
    #[Groups(['instructor:read'])]
    #[ORM\OneToMany(targetEntity: Formation::class, mappedBy: 'organizer')]
    private Collection $organizedFormations;

    /**
     * @var Collection<int, Formation>
     */
    #[Groups(['instructor:read'])]
    #[ORM\ManyToMany(targetEntity: Formation::class, mappedBy: 'instructorParticipants')]
    private Collection $participatedFormations;



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

    /**
     * @return Collection<int, Formation>
     */
    public function getOrganizedFormations(): Collection
    {
        return $this->organizedFormations;
    }

    public function addOrganizedFormation(Formation $organizedFormation): static
    {
        if (!$this->organizedFormations->contains($organizedFormation)) {
            $this->organizedFormations->add($organizedFormation);
            $organizedFormation->setOrganizer($this);
        }

        return $this;
    }

    public function removeOrganizedFormation(Formation $organizedFormation): static
    {
        if ($this->organizedFormations->removeElement($organizedFormation)) {
            // set the owning side to null (unless already changed)
            if ($organizedFormation->getOrganizer() === $this) {
                $organizedFormation->setOrganizer(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Formation>
     */
    public function getParticipatedFormations(): Collection
    {
        return $this->participatedFormations;
    }

    public function addParticipatedFormation(Formation $participatedFormation): static
    {
        if (!$this->participatedFormations->contains($participatedFormation)) {
            $this->participatedFormations->add($participatedFormation);
            $participatedFormation->addInstructorParticipant($this);
        }

        return $this;
    }

    public function removeParticipatedFormation(Formation $participatedFormation): static
    {
        if ($this->participatedFormations->removeElement($participatedFormation)) {
            $participatedFormation->removeInstructorParticipant($this);
        }

        return $this;
    }

    #[ORM\Column(length: 100, nullable: true)]
    private ?string $resetToken = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $resetTokenExpiresAt = null;

    public function getResetToken(): ?string
    {
        return $this->resetToken;
    }

    public function setResetToken(?string $resetToken): self
    {
        $this->resetToken = $resetToken;
        return $this;
    }

    public function getResetTokenExpiresAt(): ?\DateTimeInterface
    {
        return $this->resetTokenExpiresAt;
    }

    public function setResetTokenExpiresAt(?\DateTimeInterface $resetTokenExpiresAt): self
    {
        $this->resetTokenExpiresAt = $resetTokenExpiresAt;
        return $this;
    }
}
