<?php

namespace App\Entity;

use App\Repository\DojangRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: DojangRepository::class)]
class Dojang
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $name = null;

    #[ORM\Column(length: 255)]
    private ?string $city = null;

    /**
     * @var Collection<int, UserModel>
     */
    #[ORM\OneToMany(targetEntity: UserModel::class, mappedBy: 'dojang')]
    private Collection $samurangs;

    public function __construct()
    {
        $this->samurangs = new ArrayCollection();
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

    public function getCity(): ?string
    {
        return $this->city;
    }

    public function setCity(string $city): static
    {
        $this->city = $city;

        return $this;
    }

    /**
     * @return Collection<int, UserModel>
     */
    public function getSamurangs(): Collection
    {
        return $this->samurangs;
    }

    public function addSamurang(UserModel $samurang): static
    {
        if (!$this->samurangs->contains($samurang)) {
            $this->samurangs->add($samurang);
            $samurang->setDojang($this);
        }

        return $this;
    }

    public function removeSamurang(UserModel $samurang): static
    {
        if ($this->samurangs->removeElement($samurang)) {
            // set the owning side to null (unless already changed)
            if ($samurang->getDojang() === $this) {
                $samurang->setDojang(null);
            }
        }

        return $this;
    }
}
