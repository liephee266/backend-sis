<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Attribute\Groups;

#[ORM\Entity]
#[ORM\Table(name: "treatment")]
class Treatment
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: "integer")]
    #[Groups(['treatment:read', 'consultation:read'])]
    private ?int $id = null;

    #[ORM\Column(type: "text", nullable: true)]
    #[Groups(['treatment:read', 'consultation:read'])]
    private ?string $description = null;

    #[ORM\Column(type: "boolean")]
    #[Groups(['treatment:read', 'consultation:read'])]
    private bool $status;

    #[ORM\ManyToOne(inversedBy: 'treatments')]
    #[Groups(['treatment:read', 'consultation:read'])]
    private ?Consultation $consultation = null;

    // ✅ Getters & Setters

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): self
    {
        $this->description = $description;
        return $this;
    }

    public function getStatus(): bool
    {
        return $this->status;
    }

    public function setStatus(bool $status): self
    {
        $this->status = $status;
        return $this;
    }

    public function getConsultation(): ?Consultation
    {
        return $this->consultation;
    }

    public function setConsultation(?Consultation $consultation): static
    {
        $this->consultation = $consultation;

        return $this;
    }
}
