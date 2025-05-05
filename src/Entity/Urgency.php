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

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    #[Groups(["data_select","urgency:read"])]
    private ?\DateTimeInterface $created_at = null;

    #[ORM\Column(type: Types::DATE_MUTABLE)]
    #[Groups(["data_select","urgency:read"])]
    private ?\DateTimeInterface $updated_at = null;

    #[ORM\Column(length: 255)]
    #[Groups(["data_select","urgency:read"])]
    private ?string $uuid = null;

    #[ORM\ManyToOne(inversedBy: 'urgencies')]
    #[Groups(["data_select","urgency:read"])]
    private ?Hospital $tranfere_a = null;

    #[ORM\ManyToOne(inversedBy: 'urgencies')]
    #[Groups(["data_select","urgency:read"])]
    private ?Urgentist $prise_en_charge = null;

    #[ORM\Column(length: 255)]
    #[Groups(["data_select","urgency:read"])]
    private ?string $status = null;

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
    public function getUuid(): ?String
    {
        return $this->uuid;
    }

    public function setUuid(String $uuid): static
    {
        $this->uuid = $uuid;

        return $this;
    }

    public function getTranfereA(): ?Hospital
    {
        return $this->tranfere_a;
    }

    public function setTranfereA(?Hospital $tranfere_a): static
    {
        $this->tranfere_a = $tranfere_a;

        return $this;
    }

    public function getPriseEnCharge(): ?Urgentist
    {
        return $this->prise_en_charge;
    }

    public function setPriseEnCharge(?Urgentist $prise_en_charge): static
    {
        $this->prise_en_charge = $prise_en_charge;

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
