<?php

namespace App\Entity;

use App\Entity\UserModel;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use App\Repository\InstructorRepository;

#[ORM\Entity(repositoryClass: InstructorRepository::class)]
class Instructor extends UserModel
{
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
}
