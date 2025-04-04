<?php

namespace App\Entity;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Attribute\Groups;

#[ORM\Entity]
#[ORM\Table(name: "availability")]
class Availability
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: "integer")]
    #[Groups(['availability:read'])]
    private ?int $id = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    #[Groups(['availability:read'])]
    private ?\DateTimeInterface $timeInterval = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    #[Groups(['availability:read'])]
    private ?\DateTimeInterface $dateInterval = null;

    #[ORM\Column(type: Types::DATE_MUTABLE, nullable: true)]
    #[Groups(['availability:read'])]
    private ?\DateTimeInterface $date = null;

    #[ORM\ManyToOne(inversedBy: 'availabilities')]
     #[Groups(['availability:read', 'doctor:read'])]
    private ?Doctor $doctor = null;

    // ✅ Getters & Setters

    public function getId(): ?int
    {
        return $this->id;
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

    public function getDoctor(): ?Doctor
    {
        return $this->doctor;
    }

    public function setDoctor(?Doctor $doctor): static
    {
        $this->doctor = $doctor;

        return $this;
    }
}
