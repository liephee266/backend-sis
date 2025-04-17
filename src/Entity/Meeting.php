<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\Entity]
#[ORM\Table(name: "meeting")]
class Meeting
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: "integer")]
    #[Groups(["data_select","meeting:read"])]
    private ?int $id = null;

    #[ORM\ManyToOne(targetEntity: Doctor::class)]
    #[ORM\JoinColumn(name: "id_medecin", referencedColumnName: "id", nullable: false, onDelete: "CASCADE")]
    #[Groups(["meeting:read"])]
    private ?Doctor $doctor = null;

    #[ORM\Column(type: "datetime")]
    #[Groups(["meeting:read"])]
    private ?\DateTimeInterface $date = null;

    #[ORM\Column(type: "string", length: 255, nullable: true)]
    #[Groups(["meeting:read"])]
    private string $firstName;

    #[ORM\Column(type: "string", length: 255, nullable: true)]
    #[Groups(["meeting:read"])]
    private string $lastName;

    #[ORM\Column(length: 255)]
    #[Groups(["data_select","meeting:read"])]
    private ?string $motif = null;

    #[ORM\ManyToOne(inversedBy: 'meeting_id')]
    #[Groups(["meeting:read"])]
    private ?Patient $patient_id = null;

    #[ORM\ManyToOne(inversedBy: 'meetings')]
    private ?State $state_id = null;

    // ✅ Getters & Setters

    public function getId(): ?int
    {
        return $this->id;
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


    public function getDate(): ?\DateTimeInterface
    {
        return $this->date;
    }

    public function setDate(\DateTimeInterface $date): self
    {
        $this->date = $date;
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

    public function getMotif(): ?string
    {
        return $this->motif;
    }

    public function setMotif(string $motif): static
    {
        $this->motif = $motif;

        return $this;
    }

    public function getPatientId(): ?Patient
    {
        return $this->patient_id;
    }

    public function setPatientId(?Patient $patient_id): static
    {
        $this->patient_id = $patient_id;

        return $this;
    }

    public function getStateId(): ?State
    {
        return $this->state_id;
    }

    public function setStateId(?State $state_id): static
    {
        $this->state_id = $state_id;

        return $this;
    }
}
