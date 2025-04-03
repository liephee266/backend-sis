<?php

namespace App\Entity;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: "availability")]
class Availability
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: "integer")]
    private ?int $id = null;

    #[ORM\ManyToOne(targetEntity: Doctor::class)]
    #[ORM\JoinColumn(name: "id_doctor", referencedColumnName: "id", nullable: false, onDelete: "CASCADE")]
    private ?Doctor $doctor = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $timeInterval = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $dateInterval = null;

    #[ORM\Column(type: Types::DATE_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $date = null;

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

    public function getTimeInterval(): ?\DateTimeInterface
    {
        return $this->timeInterval;
    }

    public function setTimeInterval(?\DateTimeInterface $timeInterval): self
    {
        $this->timeInterval = $timeInterval;
        return $this;
    }

    public function getDateInterval(): ?\DateTimeInterface
    {
        return $this->dateInterval;
    }

    public function setDateInterval(?\DateTimeInterface $dateInterval): self
    {
        $this->dateInterval = $dateInterval;
        return $this;
    }

    public function getDate(): ?\DateTimeInterface
    {
        return $this->date;
    }

    public function setDate(?\DateTimeInterface $date): self
    {
        $this->date = $date;
        return $this;
    }
}
