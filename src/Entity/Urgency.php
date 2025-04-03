<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: "urgency")]
class Urgency
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: "integer")]
    private ?int $id = null;

    #[ORM\ManyToOne(targetEntity: Patient::class)]
    #[ORM\JoinColumn(name: "id_patient", referencedColumnName: "id", nullable: false, onDelete: "CASCADE")]
    private ?Patient $patient = null;

    #[ORM\ManyToOne(targetEntity: Hospital::class)]
    #[ORM\JoinColumn(name: "id_hospital", referencedColumnName: "id", nullable: false, onDelete: "CASCADE")]
    private ?Hospital $hospital = null;

    #[ORM\ManyToOne(targetEntity: Urgentist::class)]
    #[ORM\JoinColumn(name: "id_urgentist", referencedColumnName: "id", nullable: false, onDelete: "CASCADE")]
    private ?Urgentist $urgentist = null;

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

    public function getUrgentist(): ?Urgentist
    {
        return $this->urgentist;
    }

    public function setUrgentist(?Urgentist $urgentist): self
    {
        $this->urgentist = $urgentist;
        return $this;
    }
}
