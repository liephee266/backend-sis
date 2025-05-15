<?php

namespace App\Entity;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Uid\Uuid;

#[ORM\Entity]
#[ORM\Table(name: "meeting")]
class Meeting
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: "integer")]
    #[Groups(["data_select","meeting:read"])]
    private ?int $id = null;

    #[ORM\ManyToOne(targetEntity: Doctor::class)]
    #[ORM\JoinColumn(name: "id_medecin", referencedColumnName: "id", nullable: false, onDelete: "CASCADE")]
    #[Groups(["meeting:read"])]
    private ?Doctor $doctor = null;

    #[ORM\Column(type: Types::DATE_MUTABLE)]
    #[Groups(["meeting:read"])]
    private ?\DateTimeInterface $date = null;

    #[ORM\Column(type: Types::TIME_MUTABLE, columnDefinition: "TIME")]
    #[Groups(["meeting:read"])]
    private ?\DateTimeInterface $heure = null;

    #[ORM\Column(type: "string", length: 255, nullable: true)]
    #[Groups(["meeting:read"])]
    private string $firstName;

    #[ORM\Column(type: "string", length: 255, nullable: true)]
    #[Groups(["meeting:read"])]
    private string $lastName;

    #[ORM\Column(length: 255)]
    #[Groups(["data_select","meeting:read"])]
    private ?string $motif = null;

    #[ORM\ManyToOne(inversedBy: 'meeting_id')]
    #[Groups(["meeting:read"])]
    private ?Patient $patient_id = null;

    #[ORM\ManyToOne(inversedBy: 'meetings')]
    #[Groups(["meeting:read"])]
    private ?State $state_id = null;

    /**
     * @var Collection<int, Disponibilite>
     * 
     */
    #[ORM\OneToMany(targetEntity: Disponibilite::class, mappedBy: 'meeting')]
    #[Groups(["meeting:read"])]
    private Collection $disponibilites;

    #[ORM\Column(length: 255)]
    private ?string $uuid = null;

    #[ORM\Column(type: "datetime")]
    #[Groups(["meeting:read"])]
    private  $created_at;

    #[ORM\Column(type: Types::DATE_MUTABLE,  nullable: true)]
    #[Groups(["meeting:read"])]
    private  $updated_at;

    #[ORM\ManyToOne(inversedBy: 'meetings')]
    #[ORM\JoinColumn(nullable: false)]
    #[Groups(["meeting:read"])]
    private ?Hospital $hospital = null;

    public function __construct()
    {
        $this->uuid = Uuid::v7()->toString();
        $this->created_at = new \DateTime();
        $this->updated_at = new \DateTime();
        $this->disponibilites = new ArrayCollection();
    }
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


    public function getDate(): ?\DateTimeInterface
    {
        return $this->date;
    }

    public function setDate(\DateTimeInterface|string $date): static
    {
        if (is_string($date)) {
            $this->date = new \DateTime($date);
        } else {
            $this->date = $date;
        }
        return $this;
    }

    public function getHeure(): ?string
    {
        return $this->heure ? $this->heure->format('H:i') : null;
    }

    public function setHeure(\DateTimeInterface|string $heure): static
    {
        if (is_string($heure)) {
            $this->heure = \DateTime::createFromFormat('H:i', $heure);
            if ($this->heure === false) {
                throw new \InvalidArgumentException('Format d\'heure invalide. Utilisez HH:MM');
            }
        } else {
            // Si c'est un DateTimeInterface, on extrait juste l'heure
            $this->heure = \DateTime::createFromFormat('H:i', $heure->format('H:i'));
        }
        return $this;
    }

    public function getFirstName(): string
    {
        return $this->firstName;
    }

    public function setFirstName(string $firstName): self
    {
        $this->firstName = $firstName;
        return $this;
    }

    public function getLastName(): string
    {
        return $this->lastName;
    }

    public function setLastName(string $lastName): self
    {
        $this->lastName = $lastName;
        return $this;
    }

    public function getMotif(): ?string
    {
        return $this->motif;
    }

    public function setMotif(string $motif): static
    {
        $this->motif = $motif;

        return $this;
    }

    public function getPatientId(): ?Patient
    {
        return $this->patient_id;
    }

    public function setPatientId(?Patient $patient_id): static
    {
        $this->patient_id = $patient_id;

        return $this;
    }

    public function getStateId(): ?State
    {
        return $this->state_id;
    }

    public function setStateId(?State $state_id): static
    {
        $this->state_id = $state_id;

        return $this;
    }

    /**
     * @return Collection<int, Disponibilite>
     */
    public function getDisponibilites(): Collection
    {
        return $this->disponibilites;
    }

    public function addDisponibilite(Disponibilite $disponibilite): static
    {
        if (!$this->disponibilites->contains($disponibilite)) {
            $this->disponibilites->add($disponibilite);
            $disponibilite->setMeeting($this);
        }
        $disponibilite->setMeeting($this);
        return $this;
    }

    public function removeDisponibilite(Disponibilite $disponibilite): static
    {
        if ($this->disponibilites->removeElement($disponibilite)) {
            // set the owning side to null (unless already changed)
            if ($disponibilite->getMeeting() === $this) {
                $disponibilite->setMeeting(null);
            }
        }

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

    public function getCreatedAt(): ?\DateTimeInterface
    {
        return $this->created_at;
    }

    public function setCreatedAt(\DateTimeInterface $created_at): self
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
