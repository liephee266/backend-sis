<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: "meeting")]
class Meeting
{
    #[ORM\Id]
    #[ORM\ManyToOne(targetEntity: Doctor::class)]
    #[ORM\JoinColumn(name: "id_medecin", referencedColumnName: "id", nullable: false, onDelete: "CASCADE")]
    private ?Doctor $doctor = null;

    #[ORM\Id]
    #[ORM\ManyToOne(targetEntity: Patient::class)]
    #[ORM\JoinColumn(name: "id_patient", referencedColumnName: "id", nullable: false, onDelete: "CASCADE")]
    private ?Patient $patient = null;

    #[ORM\Column(type: "datetime")]
    private ?\DateTimeInterface $date = null;

    #[ORM\Column(type: "string", length: 255, nullable: true)]
    private ?string $motif = null;

    #[ORM\Column(type: "string", length: 255, nullable: false)]
    private string $firstName;

    #[ORM\Column(type: "string", length: 255, nullable: false)]
    private string $lastName;

    // ✅ Getters & Setters

    public function getDoctor(): ?Doctor
    {
        return $this->doctor;
    }

    public function setDoctor(?Doctor $doctor): self
    {
        $this->doctor = $doctor;
        return $this;
    }

    public function getPatient(): ?Patient
    {
        return $this->patient;
    }

    public function setPatient(?Patient $patient): self
    {
        $this->patient = $patient;
        return $this;
    }

    public function getDate(): ?\DateTimeInterface
    {
        return $this->date;
    }

    public function setDate(\DateTimeInterface $date): self
    {
        $this->date = $date;
        return $this;
    }

    public function getMotif(): ?string
    {
        return $this->motif;
    }

    public function setMotif(?string $motif): self
    {
        $this->motif = $motif;
        return $this;
    }

    public function getFirstName(): string
    {
        return $this->firstName;
    }

    public function setFirstName(string $firstName): self
    {
        $this->firstName = $firstName;
        return $this;
    }

    public function getLastName(): string
    {
        return $this->lastName;
    }

    public function setLastName(string $lastName): self
    {
        $this->lastName = $lastName;
        return $this;
    }
}
