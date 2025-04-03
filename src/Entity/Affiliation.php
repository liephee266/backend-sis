<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: "affiliation")]
class Affiliation
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: "integer")]
    private ?int $id = null;

    #[ORM\ManyToOne(targetEntity: Hospital::class)]
    #[ORM\JoinColumn(name: "id_hospital", referencedColumnName: "id", nullable: false)]
    private ?Hospital $hospital = null;

    #[ORM\ManyToOne(targetEntity: Doctor::class)]
    #[ORM\JoinColumn(name: "id_doctor", referencedColumnName: "id", nullable: false)]
    private ?Doctor $doctor = null;

    #[ORM\Column(type: "boolean")]
    private bool $state;

    // ✅ Getters & Setters

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getHospital(): ?Hospital
    {
        return $this->hospital;
    }

    public function setHospital(?Hospital $hospital): self
    {
        $this->hospital = $hospital;
        return $this;
    }

    public function getDoctor(): ?Doctor
    {
        return $this->doctor;
    }

    public function setDoctor(?Doctor $doctor): self
    {
        $this->doctor = $doctor;
        return $this;
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
}
