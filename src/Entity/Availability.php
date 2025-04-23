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
    #[Groups(["availability:read"])]
    private ?int $id = null;

    #[ORM\ManyToOne(targetEntity: Doctor::class)]
    #[ORM\JoinColumn(name: "id_doctor", referencedColumnName: "id", nullable: false, onDelete: "CASCADE")]
    #[Groups(["availability:read"])]
    private ?Doctor $doctor = null;

    #[ORM\Column(type: Types::TIME_MUTABLE)]
    #[Groups(["availability:read"])]
    private ?\DateTimeInterface $time_interval = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    #[Groups(["availability:read"])]
    private ?\DateTimeInterface $date_interval = null;

    #[ORM\Column(type: Types::DATE_MUTABLE)]
    #[Groups(["availability:read"])]
    private ?\DateTimeInterface $created_at = null;

 
    #[ORM\Column(type: Types::DATE_MUTABLE)]
    #[Groups(["availability:read"])]
    private ?\DateTimeInterface $updated_at;

    // ✅ Getters & Setters

    public function __construct()
    {
        $this->date_interval = new \DateTime();
        $this->created_at = new \DateTime();
        $this->updated_at = new \DateTime();
    }


    public function getId(): ?int
    {
        return $this->id;
    }


    public function getTimeInterval(): ?\DateTimeInterface
    {
        return $this->time_interval;
    }

    public function setTimeInterval(\DateTimeInterface $time_interval): static
    {
        $this->time_interval = $time_interval;

        return $this;
    }

    public function getDateInterval(): ?\DateTimeInterface
    {
        return $this->date_interval;
    }

    public function setDateInterval(\DateTimeInterface $date_interval): static
    {
        $this->date_interval = $date_interval;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeInterface
    {
        return $this->created_at;
    }

    public function setCreatedAt(\DateTimeInterface $created_at): static
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
