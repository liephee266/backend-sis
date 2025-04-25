<?php

namespace App\Entity;

use DateTime;
use Symfony\Component\Serializer\Annotation\Groups;
use Doctrine\DBAL\Types\Types;
use Symfony\Component\Uid\Uuid;
use Doctrine\ORM\Mapping as ORM;
use App\Repository\DisponibiliteRepository;

#[ORM\Entity(repositoryClass: DisponibiliteRepository::class)]
class Disponibilite
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'disponibilites')]
    private ?Doctor $doctor = null;

    #[ORM\Column(type: Types::DATE_MUTABLE)]
    #[Groups(["disponibilite:read","meeting:read"])]
    private ?\DateTimeInterface $date_j = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    #[Groups(["disponibilite:read","meeting:read"])]
    private ?\DateTimeInterface $heure_debut = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    #[Groups(["disponibilite:read","meeting:read"])]
    private ?\DateTimeInterface $heure_fin = null;

    #[ORM\Column]
    #[Groups(["disponibilite:read","meeting:read"])]
    private ?\DateTimeImmutable $created_at = null;

    #[ORM\Column]
    #[Groups(["disponibilite:read","meeting:read"])]
    private ?\DateTimeImmutable $updated_at = null;

    #[ORM\Column(length: 255)]
    #[Groups(["disponibilite:read","meeting:read"])]
    private ?string $uuid = null;

    #[ORM\ManyToOne(inversedBy: 'disponibilites')]
    #[Groups(["disponibilite:read","meeting:read"])]
    private ?Hospital $hospital = null;

    #[ORM\ManyToOne(inversedBy: 'disponibilites')]
    #[Groups(["disponibilite:read","meeting:read"])]
    private ?Meeting $meeting = null;

    public function __construct()
    {
        $this->uuid = Uuid::v7()->toString();
        $this->created_at = new \DateTimeImmutable();
        $this->updated_at = new \DateTimeImmutable();
    }

    public function getId(): ?int
    {
        return $this->id;
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

    public function getDateJ(): ?\DateTimeInterface
    {
        return $this->date_j;
    }

    public function setDateJ(\DateTimeInterface $date_j): static
    {
        $this->date_j = $date_j;

        return $this;
    }

    public function getHeureDebut()
    {
        return $this->heure_debut->format('H:i:s');
    }

    public function setHeureDebut(\DateTimeInterface $heure_debut): static
    {
        $this->heure_debut = $heure_debut;
        return $this;
    }

    public function getHeureFin()
    {
        return $this->heure_fin->format('H:i:s');
    }

    public function setHeureFin(\DateTimeInterface $heure_fin): static
    {
        $this->heure_fin = $heure_fin;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeImmutable
    {
        return $this->created_at;
    }

    public function setCreatedAt(\DateTimeImmutable $created_at): static
    {
        $this->created_at = $created_at;

        return $this;
    }

    public function getUpdatedAt(): ?\DateTimeImmutable
    {
        return $this->updated_at;
    }

    public function setUpdatedAt(\DateTimeImmutable $updated_at): static
    {
        $this->updated_at = $updated_at;

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

    public function getHospital(): ?Hospital
    {
        return $this->hospital;
    }

    public function setHospital(?Hospital $hospital): static
    {
        $this->hospital = $hospital;

        return $this;
    }

    public function getMeeting(): ?Meeting
    {
        return $this->meeting;
    }

    public function setMeeting(?Meeting $meeting): static
    {
        $this->meeting = $meeting;

        return $this;
    }
}
