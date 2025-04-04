<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\DBAL\Types\Types;

#[ORM\Entity]
#[ORM\Table(name: "agenda")]
class Agenda
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: "integer")]
    private ?int $id = null;

    #[ORM\Column(type: "string", length: 255, nullable: false)]
    #[Groups(['agenda:read'])]
    private ?string $listOfDays = null;

    #[ORM\Column(type: Types::TIME_MUTABLE, nullable: false)]
    #[Groups(['agenda:read'])]
    private ?\DateTimeInterface $timeInterval = null;

    #[ORM\ManyToOne(inversedBy: 'agenda')]
     #[Groups(['agenda:read', 'doctor:read'])]
    private ?Doctor $doctor = null;

    #[ORM\ManyToOne(inversedBy: 'agenda')]
     #[Groups(['agenda:read', 'hospital:read'])]
    private ?Hospital $hospital = null;

    // ✅ Getters & Setters

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getListOfDays(): ?string
    {
        return $this->listOfDays;
    }

    public function setListOfDays(string $listOfDays): self
    {
        $this->listOfDays = $listOfDays;
        return $this;
    }

    public function getTimeInterval(): ?\DateTimeInterface
    {
        return $this->timeInterval;
    }

    public function setTimeInterval(\DateTimeInterface $timeInterval): self
    {
        $this->timeInterval = $timeInterval;
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

    public function getHospital(): ?Hospital
    {
        return $this->hospital;
    }

    public function setHospital(?Hospital $hospital): static
    {
        $this->hospital = $hospital;

        return $this;
    }
}
