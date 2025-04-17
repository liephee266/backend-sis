<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Attribute\Groups;
use Symfony\Component\Uid\Uuid;

#[ORM\Entity]
#[ORM\Table(name: "service")]
class Service
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: "integer")]
    #[Groups(["data_select","service:read", "doctor:read", "meeting:read", "consultation:read", "treatment:read",
    "examination:read", "affiliation:read", "availability:read", "hospital:read"])]
    private ?int $id = null;

    #[ORM\Column(type: "string", length: 255, nullable: false)]
    #[Groups(["data_select","service:read", "doctor:read", "meeting:read", "consultation:read", "treatment:read",
    "examination:read", "affiliation:read", "availability:read", "hospital:read"])]
    private string $name;

    /**
     * @var Collection<int, Doctor>
     */
    #[ORM\OneToMany(targetEntity: Doctor::class, mappedBy: 'service')]
    private Collection $doctors;

    /**
     * @var Collection<int, Hospital>
     */
    #[ORM\ManyToMany(targetEntity: Hospital::class, mappedBy: 'services')]
    private Collection $hospital;

    #[ORM\Column(type: 'uuid')]
    #[Groups(["data_select","service:read", "doctor:read", "meeting:read", "consultation:read", "treatment:read",
    "examination:read", "affiliation:read", "availability:read", "hospital:read"])]
    private ?Uuid $uuid = null;

    #[ORM\Column(type: Types::DATE_MUTABLE)]
    #[Groups(["data_select","service:read", "doctor:read", "meeting:read", "consultation:read", "treatment:read",
    "examination:read", "affiliation:read", "availability:read", "hospital:read"])]
    private ?\DateTimeInterface $created_at = null;

    #[ORM\Column]
    #[Groups(["data_select","service:read", "doctor:read", "meeting:read", "consultation:read", "treatment:read",
    "examination:read", "affiliation:read", "availability:read", "hospital:read"])]
    private ?\DateTimeImmutable $updated_at = null;

    public function __construct()
    {
        $this->doctors = new ArrayCollection();
        $this->hospital = new ArrayCollection();
    }

    // ✅ Getters & Setters

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;
        return $this;
    }

    /**
     * @return Collection<int, Doctor>
     */
    public function getDoctors(): Collection
    {
        return $this->doctors;
    }

    public function addDoctor(Doctor $doctor): static
    {
        if (!$this->doctors->contains($doctor)) {
            $this->doctors->add($doctor);
            $doctor->setService($this);
        }

        return $this;
    }

    public function removeDoctor(Doctor $doctor): static
    {
        if ($this->doctors->removeElement($doctor)) {
            // set the owning side to null (unless already changed)
            if ($doctor->getService() === $this) {
                $doctor->setService(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Hospital>
     */
    public function getHospital(): Collection
    {
        return $this->hospital;
    }

    public function addHospital(Hospital $hospital): static
    {
        if (!$this->hospital->contains($hospital)) {
            $this->hospital->add($hospital);
            $hospital->addService($this);
        }

        return $this;
    }

    public function removeHospital(Hospital $hospital): static
    {
        if ($this->hospital->removeElement($hospital)) {
            $hospital->removeService($this);
        }

        return $this;
    }

    public function getUuid(): ?Uuid
    {
        return $this->uuid;
    }

    public function setUuid(Uuid $uuid): static
    {
        $this->uuid = $uuid;

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

    public function getUpdatedAt(): ?\DateTimeImmutable
    {
        return $this->updated_at;
    }

    public function setUpdatedAt(\DateTimeImmutable $updated_at): static
    {
        $this->updated_at = $updated_at;

        return $this;
    }

}
