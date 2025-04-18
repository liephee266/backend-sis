<?php

namespace App\Entity;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Uid\Uuid;

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


    #[ORM\Column(length: 255)]
    private ?string $uuid = null;

    #[ORM\Column(type: "datetime")]
    #[Groups(["meeting:read"])]
    private  $created_at;

    #[ORM\Column(type: Types::DATE_MUTABLE,  nullable: true)]
    #[Groups(["meeting:read"])]
    private  $updated_at;

      public function __construct()
    {
        $this->uuid = Uuid::v7()->toString();
        $this->created_at = new \DateTime();
        $this->updated_at = new \DateTime();
    }
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
    public function getUuid(): ?string
    {
        return $this->uuid;
    }

    public function setUuid(string $uuid): static
    {
        $this->uuid = $uuid;
        return $this;
    }

    public function getCreatedAt(): ?\DateTimeInterface
    {
        return $this->created_at;
    }

    public function setCreatedAt(\DateTimeInterface $created_at): self
    {
        $this->created_at = $created_at;
        return $this;
    }

    public function getUpdatedAt(): ?\DateTimeInterface
    {
        return $this->updated_at;
    }

    public function setUpdatedAt(\DateTimeInterface $updated_at): self
    {
        $this->updated_at = $updated_at;
        return $this;
    }
}
