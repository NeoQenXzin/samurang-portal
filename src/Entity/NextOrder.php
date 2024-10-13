<?php

namespace App\Entity;

use ApiPlatform\Metadata\Get;
use Doctrine\DBAL\Types\Types;
use ApiPlatform\Metadata\Patch;
use Doctrine\ORM\Mapping as ORM;
use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\GetCollection;
use App\Repository\NextOrderRepository;
use Symfony\Component\Serializer\Annotation\Groups;

#[ApiResource(
    operations: [
        new Get(normalizationContext: ['groups' => ['next_order:read']]),
        new GetCollection(normalizationContext: ['groups' => ['next_order:read']]),
        new Patch(security: "is_granted('ROLE_ADMIN')", denormalizationContext: ['groups' => ['next_order:write']])
    ],
    paginationEnabled: false,
)]
#[ORM\Entity(repositoryClass: NextOrderRepository::class)]
class NextOrder
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['next_order:read'])]
    private ?int $id = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    #[Groups(['next_order:read', 'next_order:write'])]
    private ?\DateTimeInterface $sendDate = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    #[Groups(['next_order:read', 'next_order:write'])]
    private ?\DateTimeInterface $receiveDate = null;

    #[ORM\Column(length: 25)]
    #[Groups(['next_order:read', 'next_order:write'])]
    private ?string $status = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getSendDate(): ?\DateTimeInterface
    {
        return $this->sendDate;
    }

    public function setSendDate(?\DateTimeInterface $sendDate): static
    {
        $this->sendDate = $sendDate;

        return $this;
    }

    public function getReceiveDate(): ?\DateTimeInterface
    {
        return $this->receiveDate;
    }

    public function setReceiveDate(?\DateTimeInterface $receiveDate): static
    {
        $this->receiveDate = $receiveDate;

        return $this;
    }

    public function getStatus(): ?string
    {
        return $this->status;
    }

    public function setStatus(string $status): static
    {
        $this->status = $status;

        return $this;
    }
}
