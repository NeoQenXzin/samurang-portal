<?php

namespace App\Entity;

use Stringable;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use ApiPlatform\Metadata\ApiResource;
use App\Repository\StudentRepository;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;

#[ApiResource(
    normalizationContext: ['groups' => ['student:read']],
    denormalizationContext: ['groups' => ['student:write']]
)]
#[ORM\Entity(repositoryClass: StudentRepository::class)]
class Student implements UserInterface, PasswordAuthenticatedUserInterface, \Stringable
{

    public function __toString(): string
    {
        return $this->getFirstName() . ' ' . $this->getLastName() . ' (' . $this->getMail() . ')';
    }

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['student:read', 'formation:read'])]
    private ?int $id = null;

    #[ORM\Column(length: 70)]
    #[Groups(['student:read', 'student:write', 'formation:read'])]
    private ?string $firstname = null;

    #[ORM\Column(length: 70)]
    #[Groups(['student:read', 'student:write', 'formation:read'])]
    private ?string $lastname = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    #[Groups(['student:read', 'student:write'])]
    private ?\DateTimeInterface $birthdate = null;

    #[ORM\Column(length: 255)]
    #[Groups(['student:read', 'student:write'])]
    private ?string $adress = null;

    #[ORM\Column(length: 25)]
    #[Groups(['student:read', 'student:write'])]
    private ?string $sexe = null;

    #[ORM\Column(length: 25, nullable: true)]
    #[Groups(['student:read', 'student:write'])]
    private ?string $tel = null;

    #[ORM\Column(length: 70)]
    #[Groups(['student:read', 'student:write'])]
    private ?string $mail = null;


    #[ORM\Column(length: 25, nullable: true)]
    #[Groups(['student:read', 'student:write'])]
    private ?string $passport = null;

    #[ORM\ManyToOne(inversedBy: 'students')]
    #[ORM\JoinColumn(nullable: false)]
    #[Groups(['student:read', 'student:write'])]
    private ?Grade $grade = null;

    #[ORM\ManyToOne(inversedBy: 'students')]
    #[Groups(['student:read', 'student:write'])]
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
    #[ORM\ManyToOne(inversedBy: 'students')]
    #[Groups(['student:read', 'student:write'])]
    private ?Instructor $instructor = null;

    public function getInstructor(): ?Instructor
    {
        return $this->instructor;
    }

    public function setInstructor(?Instructor $instructor): static
    {
        $this->instructor = $instructor;

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
     * @var Collection<int, Formation>
     */
    #[Groups(['student:read'])]
    #[ORM\ManyToMany(targetEntity: Formation::class, mappedBy: 'studentParticipants')]

    private Collection $participatedFormations;

    public function __construct()
    {
        $this->participatedFormations = new ArrayCollection();
    }



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
        $roles[] = 'ROLE_STUDENT';

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
    public function getParticipatedFormations(): Collection
    {
        return $this->participatedFormations;
    }

    public function addParticipatedFormation(Formation $participatedFormation): static
    {
        if (!$this->participatedFormations->contains($participatedFormation)) {
            $this->participatedFormations->add($participatedFormation);
            $participatedFormation->addStudentParticipant($this);
        }

        return $this;
    }

    public function removeParticipatedFormation(Formation $participatedFormation): static
    {
        if ($this->participatedFormations->removeElement($participatedFormation)) {
            $participatedFormation->removeStudentParticipant($this);
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
