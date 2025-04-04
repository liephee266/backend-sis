<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use App\Entity\User;
use App\Entity\Service;
use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\Entity]
#[ORM\Table(name: "doctor")]
class Doctor
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: "integer", unique: true)]
    #[Groups(["doctor:read", "affiliation:read"])]
    private ?int $id = null;

    #[ORM\ManyToOne(targetEntity: User::class)]
    #[ORM\JoinColumn(name: "user_id", referencedColumnName: "id", nullable: false, onDelete: "CASCADE")]
    #[Groups(["doctor:read"])]
    private ?User $user = null;

    #[ORM\Column(type: "string", length: 255, nullable: false)]
    #[Groups(["doctor:read"])]
    private ?string $medLisenceNumber = null;

    #[ORM\Column(type: "string", length: 255, nullable: false)]
    #[Groups(["doctor:read"])]
    private ?string $speciality = null;

    #[ORM\Column(type: "integer", nullable: false)]
    #[Groups(["doctor:read"])]
    private ?int $experience = null;

    #[ORM\Column(type: "date", nullable: false)]
    #[Groups(["doctor:read"])]
    private ?\DateTimeInterface $serviceStartingDate = null;

    #[ORM\Column(type: "string", length: 255, nullable: true)]
    #[Groups(["doctor:read"])]
    private ?string $diplome = null;

    #[ORM\Column(type: "string", length: 255, nullable: true)]
    #[Groups(["doctor:read"])]
    private ?string $other = null;

    #[ORM\Column(type: "string", length: 255, nullable: false)]
    #[Groups(["doctor:read"])]
    private ?string $cni = null;

    #[ORM\Column(type: "string", length: 255, nullable: false)]
    #[Groups(["doctor:read"])]
    private ?string $medicalLisenceCertificate = null;

    #[ORM\Column(type: "string", length: 255, nullable: false)]
    #[Groups(["doctor:read"])]
    private ?string $cv = null;

    #[ORM\ManyToOne(targetEntity: Service::class)]
    #[ORM\JoinColumn(name: "service_id", referencedColumnName: "id", nullable: false, onDelete: "CASCADE")]
    #[Groups(["doctor:read"])]
    private ?Service $service = null;

    /**
     * @var Collection<int, Affiliation>
     */
    #[ORM\OneToMany(targetEntity: Affiliation::class, mappedBy: 'doctor')]
    private Collection $affiliations;

    /**
     * @var Collection<int, Agenda>
     */
    #[ORM\OneToMany(targetEntity: Agenda::class, mappedBy: 'doctor')]
    private Collection $agenda;

    /**
     * @var Collection<int, Availability>
     */
    #[ORM\OneToMany(targetEntity: Availability::class, mappedBy: 'doctor')]
    private Collection $availabilities;

    public function __construct()
    {
        $this->affiliations = new ArrayCollection();
        $this->agenda = new ArrayCollection();
        $this->availabilities = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): self
    {
        $this->user = $user;
        return $this;
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

    public function getService(): ?Service
    {
        return $this->service;
    }

    public function setService(?Service $service): self
    {
        $this->service = $service;
        return $this;
    }

    /**
     * @return Collection<int, Affiliation>
     */
    public function getAffiliations(): Collection
    {
        return $this->affiliations;
    }

    public function addAffiliation(Affiliation $affiliation): static
    {
        if (!$this->affiliations->contains($affiliation)) {
            $this->affiliations->add($affiliation);
            $affiliation->setDoctor($this);
        }

        return $this;
    }

    public function removeAffiliation(Affiliation $affiliation): static
    {
        if ($this->affiliations->removeElement($affiliation)) {
            // set the owning side to null (unless already changed)
            if ($affiliation->getDoctor() === $this) {
                $affiliation->setDoctor(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Agenda>
     */
    public function getAgenda(): Collection
    {
        return $this->agenda;
    }

    public function addAgenda(Agenda $agenda): static
    {
        if (!$this->agenda->contains($agenda)) {
            $this->agenda->add($agenda);
            $agenda->setDoctor($this);
        }

        return $this;
    }

    public function removeAgenda(Agenda $agenda): static
    {
        if ($this->agenda->removeElement($agenda)) {
            // set the owning side to null (unless already changed)
            if ($agenda->getDoctor() === $this) {
                $agenda->setDoctor(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Availability>
     */
    public function getAvailabilities(): Collection
    {
        return $this->availabilities;
    }

    public function addAvailability(Availability $availability): static
    {
        if (!$this->availabilities->contains($availability)) {
            $this->availabilities->add($availability);
            $availability->setDoctor($this);
        }

        return $this;
    }

    public function removeAvailability(Availability $availability): static
    {
        if ($this->availabilities->removeElement($availability)) {
            // set the owning side to null (unless already changed)
            if ($availability->getDoctor() === $this) {
                $availability->setDoctor(null);
            }
        }

        return $this;
    }
}