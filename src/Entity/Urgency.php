<?php

namespace App\Entity;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Attribute\Groups;
use Symfony\Component\Uid\Uuid;

#[ORM\Entity]
#[ORM\Table(name: "urgency")]
class Urgency
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: "integer")]
    #[Groups(["data_select","urgency:read"])]
    private ?int $id = null;

    #[ORM\ManyToOne(targetEntity: Patient::class)]
    #[ORM\JoinColumn(name: "id_patient", referencedColumnName: "id", nullable: false, onDelete: "CASCADE")]
    #[Groups(["data_select","urgency:read"])]
    private ?Patient $patient = null;

    #[ORM\ManyToOne(targetEntity: Hospital::class)]
    #[ORM\JoinColumn(name: "id_hospital", referencedColumnName: "id", nullable: false, onDelete: "CASCADE")]
    #[Groups(["data_select","urgency:read"])]
    private ?Hospital $hospital = null;

    #[ORM\ManyToOne(inversedBy: 'urgencies')]
    #[ORM\JoinColumn(nullable: false)]
    #[Groups(["data_select","urgency:read"])]
    private ?User $user = null;

    #[ORM\Column(length: 255)]
    #[Groups(["data_select","urgency:read"])]
    private ?string $description = null;

    #[ORM\Column(type: Types::DATE_MUTABLE)]
    #[Groups(["data_select","urgency:read"])]
    private ?\DateTimeInterface $created_at = null;

    #[ORM\Column(type: Types::DATE_MUTABLE)]
    #[Groups(["data_select","urgency:read"])]
    private ?\DateTimeInterface $updated_at = null;

    #[ORM\Column(length: 255)]
    #[Groups(["data_select","urgency:read"])]
    private ?string $uuid = null;

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

    public function getPatient(): ?Patient
    {
        return $this->patient;
    }

    public function setPatient(?Patient $patient): self
    {
        $this->patient = $patient;
        return $this;
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

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): static
    {
        $this->user = $user;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): static
    {
        $this->description = $description;

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

    public function setUpdatedAt(\DateTimeInterface $updated_at): static
    {
        $this->updated_at = $updated_at;

        return $this;
    }
    public function getUuid(): ?Uuid
    {
        return $this->uuid;
    }

    public function setUuid(Uuid $uuid): static
    {
        $this->uuid = $uuid;

        return $this;
    }


}
