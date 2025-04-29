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
    #[Groups(["disponibilite:read", "meeting:read"])]
    private ?\DateTimeInterface $date_j = null;

    #[ORM\Column(type: Types::TIME_MUTABLE, columnDefinition: "TIME")]
    #[Groups(["disponibilite:read", "meeting:read"])]
    private ?\DateTimeInterface $heure_debut = null;
    
    #[ORM\Column(type: Types::TIME_MUTABLE, columnDefinition: "TIME")]
    #[Groups(["disponibilite:read", "meeting:read"])]
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
    #[Groups(["disponibilite:read"])]
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

    public function setDateJ(\DateTimeInterface|string $date_j): static
    {
        if (is_string($date_j)) {
            $this->date_j = new \DateTime($date_j);
        } else {
            $this->date_j = $date_j;
        }
        return $this;
    }

    public function getHeureDebut(): ?string
    {
        return $this->heure_debut ? $this->heure_debut->format('H:i') : null;
    }

    public function setHeureDebut(\DateTimeInterface|string $heure_debut): static
    {
        if (is_string($heure_debut)) {
            $this->heure_debut = \DateTime::createFromFormat('H:i', $heure_debut);
            if ($this->heure_debut === false) {
                throw new \InvalidArgumentException('Format d\'heure invalide. Utilisez HH:MM');
            }
        } else {
            // Si c'est un DateTimeInterface, on extrait juste l'heure
            $this->heure_debut = \DateTime::createFromFormat('H:i', $heure_debut->format('H:i'));
        }
        return $this;
    }

    public function getHeureFin(): ?string
    {
        return $this->heure_fin ? $this->heure_fin->format('H:i') : null;
    }

    public function setHeureFin(\DateTimeInterface|string $heure_fin): static
    {
        if (is_string($heure_fin)) {
            $this->heure_fin = \DateTime::createFromFormat('H:i', $heure_fin);
            if ($this->heure_fin === false) {
                throw new \InvalidArgumentException('Format d\'heure invalide. Utilisez HH:MM');
            }
        } else {
            $this->heure_fin = \DateTime::createFromFormat('H:i', $heure_fin->format('H:i'));
        }
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
