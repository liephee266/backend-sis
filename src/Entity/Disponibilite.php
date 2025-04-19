<?php

namespace App\Entity;

use App\Repository\DisponibiliteRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: DisponibiliteRepository::class)]
class Disponibilite
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'disponibilites')]
    private ?Doctor $Medecin = null;

    #[ORM\Column(type: Types::DATE_MUTABLE)]
    private ?\DateTimeInterface $date_j = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $heure_debut = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $heure_fin = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $created_at = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $updated_at = null;

    #[ORM\Column(length: 255)]
    private ?string $uuid = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getMedecin(): ?Doctor
    {
        return $this->Medecin;
    }

    public function setMedecin(?Doctor $Medecin): static
    {
        $this->Medecin = $Medecin;

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

    public function getHeureDebut(): ?\DateTimeInterface
    {
        return $this->heure_debut;
    }

    public function setHeureDebut(\DateTimeInterface $heure_debut): static
    {
        $this->heure_debut = $heure_debut;

        return $this;
    }

    public function getHeureFin(): ?\DateTimeInterface
    {
        return $this->heure_fin;
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
}
