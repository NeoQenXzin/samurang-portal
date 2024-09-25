<?php

namespace App\Entity;

use App\Repository\GradeRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: GradeRepository::class)]
class Grade
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $name = null;

    /**
     * @var Collection<int, UserModel>
     */
    #[ORM\OneToMany(targetEntity: UserModel::class, mappedBy: 'grade')]
    private Collection $userModels;

    public function __construct()
    {
        $this->userModels = new ArrayCollection();
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
     * @return Collection<int, UserModel>
     */
    public function getUserModels(): Collection
    {
        return $this->userModels;
    }

    public function addUserModel(UserModel $userModel): static
    {
        if (!$this->userModels->contains($userModel)) {
            $this->userModels->add($userModel);
            $userModel->setGrade($this);
        }

        return $this;
    }

    public function removeUserModel(UserModel $userModel): static
    {
        if ($this->userModels->removeElement($userModel)) {
            // set the owning side to null (unless already changed)
            if ($userModel->getGrade() === $this) {
                $userModel->setGrade(null);
            }
        }

        return $this;
    }
}
