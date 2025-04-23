<?php

namespace App\Entity;

use DateTime;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Attribute\Groups;

#[ORM\Entity]
#[ORM\Table(name: "affiliation")]
class Affiliation
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: "integer")]
    #[Groups(["affiliation:read"])]
    private ?int $id = null;

    #[ORM\ManyToOne(targetEntity: Hospital::class)]
    #[ORM\JoinColumn(name: "id_hospital", referencedColumnName: "id", nullable: false)]
    #[Groups(["affiliation:read"])]
    private ?Hospital $hospital = null;

    #[ORM\ManyToOne(targetEntity: Doctor::class)]
    #[ORM\JoinColumn(name: "id_doctor", referencedColumnName: "id", nullable: false)]
    #[Groups(["affiliation:read"])]
    private ?Doctor $doctor = null;

    #[ORM\Column(type: "boolean")]
    #[Groups(["affiliation:read"])]
    private bool $state;

    #[ORM\Column(type: Types::DATE_MUTABLE)]
    #[Groups(["affiliation:read"])]
    private ?\DateTimeInterface $created_at;

    #[ORM\Column(type: Types::DATE_MUTABLE)]
    #[Groups(["affiliation:read"])]
    private ?\DateTimeInterface $updated_at;

    // ✅ Getters & Setters
    public function __construct()
    {
        $this->created_at = new \DateTimeImmutable('now');
        $this->updated_at = new \DateTimeImmutable('now');
    }

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

    public function getCreatedAt(): ?\DateTimeInterface
    {
        return $this->created_at;
    }

    public function setCreatedAt(\DateTimeInterface $created_at): static
    {
        $this->created_at = $created_at;

        return $this;
    }

    public function getUpdatedAt(): ?\DateTimeInterface
    {
        return $this->updated_at;
    }

    public function setUpdatedAt(\DateTimeInterface $updated_at): static
    {
        $this->updated_at = $updated_at;

        return $this;
    }
}
