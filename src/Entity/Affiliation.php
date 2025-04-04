<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Attribute\Groups;

#[ORM\Entity]
#[ORM\Table(name: "affiliation")]
class Affiliation
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: "integer")]
    #[Groups(['affiliation:read'])]
    private ?int $id = null;


    #[ORM\Column(type: "boolean")]
    #[Groups(['affiliation:read'])]
    private bool $state;

    #[ORM\ManyToOne(inversedBy: 'affiliations')]
    #[Groups(['affiliation:read', 'hospital:read'])]
    private ?Hospital $hospital = null;

    #[ORM\ManyToOne(inversedBy: 'affiliations')]
    #[Groups(['affiliation:read', 'doctor:read'])]
    private ?Doctor $doctor = null;

    // ✅ Getters & Setters

    public function getId(): ?int
    {
        return $this->id;
    }


    public function getState(): bool
    {
        return $this->state;
    }

    public function setState(bool $state): self
    {
        $this->state = $state;
        return $this;
    }

    public function getHospital(): ?Hospital
    {
        return $this->hospital;
    }

    public function setHospital(?Hospital $hospital): static
    {
        $this->hospital = $hospital;

        return $this;
    }

    public function getDoctor(): ?Doctor
    {
        return $this->doctor;
    }

    public function setDoctor(?Doctor $doctor): static
    {
        $this->doctor = $doctor;

        return $this;
    }
}
