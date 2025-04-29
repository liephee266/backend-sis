<?php

namespace App\Entity;

use App\Entity\User;
use App\Entity\Service;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Attribute\Groups;

#[ORM\Entity]
#[ORM\Table(name: "doctor")]
class Doctor
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: "integer", unique: true)]
    #[Groups(["data_select","doctor:read","meeting:read", "consultation:read","treatment:read", "examination:read",
    "affiliation:read", "agenda:read", "availability:read", "dossier_medicale:read", "hospital:read",
    "HistoriqueMedical:read","patient:read", "hospitaladmin:read"])]
    private ?int $id = null;

    #[ORM\ManyToOne(targetEntity: User::class)]
    #[ORM\JoinColumn(name: "user_id", referencedColumnName: "id", nullable: false, onDelete: "CASCADE")]
    #[Groups(["data_select","doctor:read", "meeting:read", "consultation:read", "treatment:read", "examination:read",
    "affiliation:read", "agenda:read", "availability:read", "dossier_medicale:read",
    "hospital:read", "hospitaladmin:read"])]
    private ?User $user = null;

    #[ORM\Column(type: "string", length: 255, nullable: false)]
    #[Groups(["doctor:read", "consultation:read", "affiliation:read", 
    "availability:read", "hospital:read", "hospitaladmin:read"])]
    private ?string $medLisenceNumber = null;

    #[ORM\Column(type: "string", length: 255, nullable: false)]
    #[Groups(["doctor:read", "meeting:read", "consultation:read", "treatment:read", "examination:read",
    "affiliation:read", "agenda:read", "availability:read", 
    "dossier_medicale:read", "hospital:read", "hospitaladmin:read"])]
    private ?string $speciality = null;

    #[ORM\Column(type: "integer", nullable: false)]
    #[Groups(["doctor:read", "consultation:read", "affiliation:read", 
    "availability:read", "hospital:read", "hospitaladmin:read"])]
    private ?int $experience = null;

    #[ORM\Column(type: "date", nullable: false)]
    #[Groups(["doctor:read", "consultation:read", "affiliation:read",
    "availability:read", "hospital:read", "hospitaladmin:read"])]
    private ?\DateTimeInterface $serviceStartingDate = null;

    #[ORM\Column(type: "string", length: 255, nullable: true)]
    #[Groups(["doctor:read", "consultation:read", "affiliation:read", 
    "availability:read", "hospital:read", "hospitaladmin:read"])]
    private ?string $diplome = null;

    #[ORM\Column(type: "string", length: 255, nullable: true)]
    #[Groups(["doctor:read", "consultation:read", "affiliation:read", 
    "availability:read", "hospital:read", "hospitaladmin:read"])]
    private ?string $other = null;

    #[ORM\Column(type: "string", length: 255, nullable: false)]
    #[Groups(["doctor:read", "consultation:read", "affiliation:read",
    "availability:read", "hospital:read", "hospitaladmin:read"])]
    private ?string $cni = null;

    #[ORM\Column(type: "string", length: 255, nullable: false)]
    #[Groups(["doctor:read", "consultation:read", "affiliation:read",
    "availability:read", "hospital:read", "hospitaladmin:read"])]
    private ?string $medicalLisenceCertificate = null;

    #[ORM\Column(type: "string", length: 255, nullable: false)]
    #[Groups(["doctor:read", "consultation:read", "affiliation:read",
    "availability:read", "hospital:read", "hospitaladmin:read"])]
    private ?string $cv = null;

    #[ORM\ManyToOne(targetEntity: Service::class)]
    #[ORM\JoinColumn(name: "service_id", referencedColumnName: "id", nullable: true, onDelete: "CASCADE")]
    #[Groups(["doctor:read", "meeting:read", "consultation:read", "treatment:read", "examination:read",
    "affiliation:read", "agenda:read", "availability:read", "dossier_medicale:read", "hospital:read", "hospitaladmin:read"])]
    private ?Service $service = null;

    #[ORM\Column(nullable: true)]
    #[Groups(["hospital:read"])]
    private ?bool $isArchived = null;

    #[ORM\Column(nullable: true)]
    #[Groups(["hospital:read"])]
    private ?bool $isSuspended = null;

    /**
     * @var Collection<int, Hospital>
     */
    #[ORM\ManyToMany(targetEntity: Hospital::class, inversedBy: 'doctors')]
    #[Groups("doctor:read")]
    private Collection $hospital;

    /**
     * @var Collection<int, Disponibilite>
     */
    #[ORM\OneToMany(targetEntity: Disponibilite::class, mappedBy: 'Medecin')]
    private Collection $disponibilites;

    /**
     * @var Collection<int, HistoriqueMedical>
     */
    #[ORM\OneToMany(targetEntity: HistoriqueMedical::class, mappedBy: 'medecinTraitant')]
    private Collection $historiqueMedicals;

    public function __construct()
    {
        $this->hospital = new ArrayCollection();
        $this->historiqueMedicals = new ArrayCollection();
        $this->disponibilites = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getMedLisenceNumber(): ?string
    {
        return $this->medLisenceNumber;
    }

    public function setMedLisenceNumber(string $medLisenceNumber): self
    {
        $this->medLisenceNumber = $medLisenceNumber;
        return $this;
    }

    public function getSpeciality(): ?string
    {
        return $this->speciality;
    }

    public function setSpeciality(string $speciality): self
    {
        $this->speciality = $speciality;
        return $this;
    }

    public function getExperience(): ?int
    {
        return $this->experience;
    }

    public function setExperience(int $experience): self
    {
        $this->experience = $experience;
        return $this;
    }

    public function getServiceStartingDate(): ?\DateTimeInterface
    {
        return $this->serviceStartingDate;
    }

    public function setServiceStartingDate(\DateTimeInterface $serviceStartingDate): self
    {
        $this->serviceStartingDate = $serviceStartingDate;
        return $this;
    }

    public function getDiplome(): ?string
    {
        return $this->diplome;
    }

    public function setDiplome(?string $diplome): self
    {
        $this->diplome = $diplome;
        return $this;
    }

    public function getOther(): ?string
    {
        return $this->other;
    }

    public function setOther(?string $other): self
    {
        $this->other = $other;
        return $this;
    }

    public function getCni(): ?string
    {
        return $this->cni;
    }

    public function setCni(string $cni): self
    {
        $this->cni = $cni;
        return $this;
    }

    public function getMedicalLisenceCertificate(): ?string
    {
        return $this->medicalLisenceCertificate;
    }

    public function setMedicalLisenceCertificate(string $medicalLisenceCertificate): self
    {
        $this->medicalLisenceCertificate = $medicalLisenceCertificate;
        return $this;
    }

    public function getCv(): ?string
    {
        return $this->cv;
    }

    public function setCv(string $cv): self
    {
        $this->cv = $cv;
        return $this;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): static
    {
        $this->user = $user;

        return $this;
    }

    public function getService(): ?Service
    {
        return $this->service;
    }

    public function setService(?Service $service): static
    {
        $this->service = $service;

        return $this;
    }

    public function isArchived(): ?bool
    {
        return $this->isArchived;
    }

    public function setIsArchived(?bool $isArchived): static
    {
        $this->isArchived = $isArchived;

        return $this;
    }

    public function isSuspended(): ?bool
    {
        return $this->isSuspended;
    }

    public function setIsSuspended(?bool $isSuspended): static
    {
        $this->isSuspended = $isSuspended;

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
        }

        return $this;
    }

    public function removeHospital(Hospital $hospital): static
    {
        $this->hospital->removeElement($hospital);

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
            $disponibilite->setDoctor($this);
        }

        return $this;
    }

    public function removeDisponibilite(Disponibilite $disponibilite): static
    {
        if ($this->disponibilites->removeElement($disponibilite)) {
            // set the owning side to null (unless already changed)
            if ($disponibilite->getDoctor() === $this) {
                $disponibilite->setDoctor(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, HistoriqueMedical>
     */
    public function getHistoriqueMedicals(): Collection
    {
        return $this->historiqueMedicals;
    }
}