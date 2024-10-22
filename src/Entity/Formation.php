<?php

namespace App\Entity;

use App\Entity\Student;
use App\Entity\Instructor;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\Put;
use ApiPlatform\Metadata\Post;
use Doctrine\DBAL\Types\Types;
use ApiPlatform\Metadata\Delete;
use Doctrine\ORM\Mapping as ORM;
use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\GetCollection;
use App\Repository\FormationRepository;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Serializer\Annotation\Groups;

#[ApiResource(
    operations: [
        new Get(normalizationContext: ['groups' => ['formation:read', 'formation:participants']]),
        new GetCollection(normalizationContext: ['groups' => ['formation:read']]),
        new Post(security: "is_granted('ROLE_INSTRUCTOR')"),
        new Put(security: "is_granted('ROLE_INSTRUCTOR') and object.getOrganizer() == user"),
        new Delete(security: "is_granted('ROLE_INSTRUCTOR') and object.getOrganizer() == user")
    ],
    normalizationContext: ['groups' => ['formation:read']],
    denormalizationContext: ['groups' => ['formation:write']]
)]
#[ORM\Entity(repositoryClass: FormationRepository::class)]
class Formation
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['formation:read'])]
    private ?int $id = null;

    #[ORM\Column(length: 50)]
    #[Groups(['formation:read', 'formation:write'])]
    private ?string $type = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    #[Groups(['formation:read', 'formation:write'])]
    private ?string $description = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    #[Groups(['formation:read', 'formation:write'])]
    private ?\DateTimeInterface $startDate = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    #[Groups(['formation:read', 'formation:write'])]
    private ?\DateTimeInterface $endDate = null;

    #[ORM\Column(length: 255)]
    #[Groups(['formation:read', 'formation:write'])]
    private ?string $location = null;

    #[Groups(['formation:read'])]
    #[ORM\Column]
    private ?int $participantsCount = 0;

    #[ORM\Column(length: 255, nullable: true)]
    #[Groups(['formation:read', 'formation:write'])]
    private ?string $url = null;

    #[ORM\ManyToOne(inversedBy: 'organizedFormations')]
    #[Groups(['formation:read', 'formation:write'])]
    private ?Instructor $organizer = null;

    /**
     * @var Collection<int, Instructor>
     */
    #[Groups(['formation:read', 'formation:participants'])]
    #[ORM\ManyToMany(targetEntity: Instructor::class, inversedBy: 'participatedFormations')]
    private Collection $instructorParticipants;

    /**
     * @var Collection<int, Student>
     */ 
    #[Groups(['formation:read', 'formation:participants'])]
    #[ORM\ManyToMany(targetEntity: Student::class, inversedBy: 'participatedFormations')]
    private Collection $studentParticipants;

    public function __construct()
    {
        $this->instructorParticipants = new ArrayCollection();
        $this->studentParticipants = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getType(): ?string
    {
        return $this->type;
    }

    public function setType(string $type): static
    {
        $this->type = $type;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): static
    {
        $this->description = $description;

        return $this;
    }

    public function getStartDate(): ?\DateTimeInterface
    {
        return $this->startDate;
    }

    public function setStartDate(\DateTimeInterface $startDate): static
    {
        $this->startDate = $startDate;

        return $this;
    }

    public function getEndDate(): ?\DateTimeInterface
    {
        return $this->endDate;
    }

    public function setEndDate(\DateTimeInterface $endDate): static
    {
        $this->endDate = $endDate;

        return $this;
    }

    public function getLocation(): ?string
    {
        return $this->location;
    }

    public function setLocation(string $location): static
    {
        $this->location = $location;

        return $this;
    }

    public function getParticipantsCount(): ?int
    {
        return $this->participantsCount;
    }

    public function setParticipantsCount(int $participantsCount): static
    {
        $this->participantsCount = $participantsCount;

        return $this;
    }

    public function getUrl(): ?string
    {
        return $this->url;
    }

    public function setUrl(?string $url): static
    {
        $this->url = $url;

        return $this;
    }

    public function getOrganizer(): ?Instructor
    {
        return $this->organizer;
    }

    public function setOrganizer(?Instructor $organizer): static
    {
        $this->organizer = $organizer;

        return $this;
    }

    /**
     * @return Collection<int, Instructor>
     */
    public function getInstructorParticipants(): Collection
    {
        return $this->instructorParticipants;
    }

    public function addInstructorParticipant(Instructor $instructorParticipant): static
    {
        if (!$this->instructorParticipants->contains($instructorParticipant)) {
            $this->instructorParticipants->add($instructorParticipant);
        }

        return $this;
    }

    public function removeInstructorParticipant(Instructor $instructorParticipant): static
    {
        $this->instructorParticipants->removeElement($instructorParticipant);

        return $this;
    }

    /**
     * @return Collection<int, Student>
     */
    public function getStudentParticipants(): Collection
    {
        return $this->studentParticipants;
    }

    public function addStudentParticipant(Student $studentParticipant): static
    {
        if (!$this->studentParticipants->contains($studentParticipant)) {
            $this->studentParticipants->add($studentParticipant);
        }

        return $this;
    }

    public function removeStudentParticipant(Student $studentParticipant): static
    {
        $this->studentParticipants->removeElement($studentParticipant);

        return $this;
    }

    public function addParticipant($participant): static
{
    if ($participant instanceof Instructor) {
        if (!$this->instructorParticipants->contains($participant)) {
            $this->instructorParticipants->add($participant);
            $this->participantsCount++;
        }
    } elseif ($participant instanceof Student) {
        if (!$this->studentParticipants->contains($participant)) {
            $this->studentParticipants->add($participant);
            $this->participantsCount++;
        }
    }

    return $this;
}

public function removeParticipant($participant): static
{
    if ($participant instanceof Instructor) {
        if ($this->instructorParticipants->removeElement($participant)) {
            $this->participantsCount--;
        }
    } elseif ($participant instanceof Student) {
        if ($this->studentParticipants->removeElement($participant)) {
            $this->participantsCount--;
        }
    }

    return $this;
}

public function isParticipant($participant): bool
{
    if ($participant instanceof Instructor) {
        return $this->instructorParticipants->contains($participant);
    } elseif ($participant instanceof Student) {
        return $this->studentParticipants->contains($participant);
    }
    return false;
}
}
