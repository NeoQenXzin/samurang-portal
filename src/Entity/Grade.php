<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use App\Repository\GradeRepository;
use ApiPlatform\Metadata\ApiResource;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Serializer\Annotation\Groups;

#[ApiResource(
    normalizationContext: ['groups' => ['read']]
)]
#[ORM\Entity(repositoryClass: GradeRepository::class)]
class Grade implements \Stringable
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[Groups(['read'])]
    #[ORM\Column(length: 255)]
    private ?string $name = null;

    #[ORM\OneToMany(targetEntity: Instructor::class, mappedBy: 'grade')]
    private Collection $instructors;

    #[ORM\OneToMany(targetEntity: Student::class, mappedBy: 'grade')]
    private Collection $students;

    public function __construct()
    {
        $this->instructors = new ArrayCollection();
        $this->students = new ArrayCollection();
    }

    public function __toString(): string
    {
        return $this->getName();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): static
    {
        $this->name = $name;

        return $this;
    }

    /**
     * @return Collection<int, Instructor>
     */
    public function getInstructors(): Collection
    {
        return $this->instructors;
    }

    public function addInstructor(Instructor $instructor): static
    {
        if (!$this->instructors->contains($instructor)) {
            $this->instructors->add($instructor);
            $instructor->setGrade($this);
        }

        return $this;
    }

    public function removeInstructor(Instructor $instructor): static
    {
        if ($this->instructors->removeElement($instructor)) {
            if ($instructor->getGrade() === $this) {
                $instructor->setGrade(null);
            }
        }

        return $this;
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
            $student->setGrade($this);
        }

        return $this;
    }

    public function removeStudent(Student $student): static
    {
        if ($this->students->removeElement($student)) {
            if ($student->getGrade() === $this) {
                $student->setGrade(null);
            }
        }

        return $this;
    }
}
