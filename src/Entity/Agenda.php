<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\DBAL\Types\Types;
use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\Entity]
#[ORM\Table(name: "agenda")]
class Agenda
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: "integer")]
    #[Groups(["agenda:read"])]
    private ?int $id = null;

    #[ORM\ManyToOne(targetEntity: Doctor::class)]
    #[ORM\JoinColumn(name: "id_doctor", referencedColumnName: "id", nullable: false, onDelete: "CASCADE")]
    #[Groups(["agenda:read"])]
    private ?Doctor $doctor = null;

    #[ORM\ManyToOne(targetEntity: Hospital::class)]
    #[ORM\JoinColumn(name: "id_hospital", referencedColumnName: "id", nullable: false, onDelete: "CASCADE")]
    #[Groups(["agenda:read"])]
    private ?Hospital $hospital = null;

    #[ORM\Column(type: "string", length: 255, nullable: false)]
    #[Groups(["agenda:read"])]
    private ?string $listOfDays = null;

    #[ORM\Column(type: Types::TIME_MUTABLE, nullable: false)]
    #[Groups(["agenda:read"])]
    private ?\DateTimeInterface $timeInterval = null;

    // ✅ Getters & Setters

    public function __construct()
    {
        $this->timeInterval = new \DateTimeImmutable();
    }

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

    public function getHospital(): ?Hospital
    {
        return $this->hospital;
    }

    public function setHospital(?Hospital $hospital): self
    {
        $this->hospital = $hospital;
        return $this;
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
}
