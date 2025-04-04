<?php

namespace App\Entity;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Attribute\Groups;

#[ORM\Entity]
#[ORM\Table(name: "disponibility")]
class Disponibility
{
  #[ORM\Id]
  #[ORM\GeneratedValue]
  #[ORM\Column(type: "integer", unique: true)]
  #[Groups(["disponibility:read"])]
  private ?int $id = null;

  #[ORM\ManyToOne(targetEntity: Doctor::class)]
  #[ORM\JoinColumn(name: "id_doctor", referencedColumnName: "id", nullable: false, onDelete: "CASCADE")]
  private ?int $id_doctor = null;

  #[ORM\Column(type: Types::DATE_MUTABLE, nullable: true)]
  #[Groups(["disponibility:read"])]
  private ?\DateTimeInterface $date = null;

  #[ORM\Column(type: Types::DATE_MUTABLE, nullable: true)]
  #[Groups(["disponibility:read"])]
  private ?\DateTimeInterface $date_interval = null;

  public function getId(): ?int
  {
    return $this->id;
  }

  public function setId(int $id): static
  {
    $this->id = $id;

    return $this;
  }

  public function getIdDoctor(): ?int
  {
    return $this->id_doctor;
  }

  public function setIdDoctor(int $id_doctor): static
  {
    $this->id_doctor = $id_doctor;

    return $this;
  }

  public function getDate(): ?\DateTimeInterface
  {
    return $this->date;
  }

  public function setDate(?\DateTimeInterface $date): static
  {
    $this->date = $date;

    return $this;
  }

  public function getDateInterval(): ?\DateTimeInterface
  {
    return $this->date_interval;
  }

  public function setDateInterval(?\DateTimeInterface $date_interval): static
  {
    $this->date_interval = $date_interval;

    return $this;
  }
}
