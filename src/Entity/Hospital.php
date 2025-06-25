<?php

namespace App\Entity;

use App\Entity\Status;
use App\Entity\Service;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Uid\Uuid;

#[ORM\Entity]
#[ORM\Table(name: "hospital")]
class Hospital
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: "integer", unique: true)]
    #[Groups(["data_select","hospital:read", "urgency:read", "consultation:read", "treatment:read",'urgentist:read','online:read',
    "examination:read", "hospitaladmin:read", "affiliation:read", "agenda:read", "dossier_medicale:read","urgency:read",
    "doctor:read","HistoriqueMedical:read", "patient:read", "agenthospital:read", "disponibilite:read", "meeting:read"])]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Groups(["hospital:read", "urgency:read", "consultation:read", "treatment:read",'urgentist:read',"urgency:read",'online:read',
    "examination:read", "hospitaladmin:read", "affiliation:read", "agenda:read", "dossier_medicale:read","meeting:read",
    "hospital:read", "doctor:read", "HistoriqueMedical:read", "patient:read", "agenthospital:read", "disponibilite:read"])]
    private ?string $uuid = null;

    #[ORM\Column(type: "string", length: 255, nullable: false)]
    #[Groups(["data_select","hospital:read","urgency:read", "consultation:read", "treatment:read",'urgentist:read','online:read',
    "examination:read", "hospitaladmin:read", "affiliation:read", "agenda:read", "dossier_medicale:read", "doctor:read",
    "HistoriqueMedical:read", "patient:read", "agenthospital:read", "disponibilite:read","urgency:read","meeting:read"])]
    private ?string $name = null;

    #[ORM\Column(type: "string", length: 255, nullable: false)]
    #[Groups(["hospital:read", "urgency:read", "consultation:read", "treatment:read",'urgentist:read',"meeting:read",
    "examination:read", "hospitaladmin:read", "affiliation:read", "agenda:read", "dossier_medicale:read", "doctor:read",
    "HistoriqueMedical:read", "patient:read", "agenthospital:read", "disponibilite:read","urgency:read",'online:read'])]
    private ?string $address = null;
    

    #[ORM\Column(type: "string", length: 255, nullable: false)]
    #[Groups(["hospital:read", "urgency:read", "consultation:read", "treatment:read",'urgentist:read',"meeting:read",
    "examination:read", "hospitaladmin:read", "affiliation:read", "agenda:read", "dossier_medicale:read", "doctor:read",
    "HistoriqueMedical:read", "patient:read", "agenthospital:read", "disponibilite:read","urgency:read",'online:read'])]
    private ?string $clientServiceTel = null;

    #[ORM\Column(type: "string", length: 255, nullable: false)]
    #[Groups(['hospital:read', "urgency:read", "consultation:read", "treatment:read",'urgentist:read',"meeting:read",
    "examination:read", "hospitaladmin:read", "affiliation:read", "agenda:read", "dossier_medicale:read", "doctor:read",
    "HistoriqueMedical:read", "patient:read", "agenthospital:read", "disponibilite:read","urgency:read",'online:read'])]
    private ?string $email = null;

    #[ORM\Column(type: "text", nullable: true)]
    #[Groups(['hospital:read', "urgency:read", "consultation:read", "treatment:read",'urgentist:read',"meeting:read",
    "examination:read", "hospitaladmin:read", "affiliation:read", "agenda:read", "dossier_medicale:read", "doctor:read",
    "HistoriqueMedical:read", "patient:read", "agenthospital:read", "disponibilite:read","urgency:read",'online:read'])]
    private ?string $webSite = null;

    #[ORM\Column(type: "string", length: 255, nullable: false)]
    #[Groups(['hospital:read', "hospitaladmin:read"])]
    private ?string $registrationNumber = null;

    #[ORM\Column(type: "string", length: 255, nullable: false)]
    #[Groups(['hospital:read', "hospitaladmin:read", "disponibilite:read"])]
    private ?string $ceo = null;

    #[ORM\Column(type: "string", length: 255, nullable: false)]
    #[Groups(['hospital:read', "hospitaladmin:read"])]
    private ?string $accreditation = null;

    #[ORM\Column(type: "string", length: 255, nullable: false)]
    #[Groups(['hospital:read', "hospitaladmin:read"])]
    private ?string $niu = null;

    #[ORM\Column(type: "string", length: 255, nullable: false)]
    #[Groups(['hospital:read', "hospitaladmin:read"])]
    private ?string $rccm = null;

    #[ORM\Column(type: "boolean", nullable: false)]
    #[Groups(['hospital:read', "urgency:read", "hospitaladmin:read","urgentist:read"])]
    private ?bool $hasUrgency = false;

    #[ORM\Column(type: "boolean", nullable: false)]
    #[Groups(['hospital:read', "urgency:read", "hospitaladmin:read",'urgentist:read'])]
    private ?bool $hasAmbulance = false;

    #[ORM\Column(type: "string", length: 255, nullable: false)]
    #[Groups(['hospital:read', "hospitaladmin:read"])]
    private ?string $exploitationLisence = null;

    #[ORM\Column(type: "string", length: 255, nullable: false)]
    #[Groups(['hospital:read', "hospitaladmin:read"])]
    private ?string $accreditationCertificate = null;

    #[ORM\Column(type: "string", length: 255, nullable: true)]
    #[Groups(['hospital:read', "urgency:read", "consultation:read", "treatment:read",'urgentist:read',"meeting:read",
    "examination:read", "hospitaladmin:read", "affiliation:read", "agenda:read", "dossier_medicale:read", "doctor:read",
    "HistoriqueMedical:read", "patient:read", "user:read", "disponibilite:read","urgency:read"])]
    private ?string $logo = null;

    #[ORM\Column(nullable: true)]
    #[Groups(['hospital:read', "hospitaladmin:read"])]
    private ?bool $isArchived = null;

    #[ORM\ManyToOne(inversedBy: 'hospitals')]
    #[ORM\JoinColumn(nullable: false)]
    #[Groups(['hospital:read', "hospitaladmin:read"])]
    private ?Status $status = null;

    #[ORM\Column(type: "datetime")]
    #[Groups(['hospital:read', "hospitaladmin:read", "doctor:read","agenthospital:read"])]
    private $created_at;

    #[ORM\Column(type: "datetime")]
    #[Groups(['hospital:read', "hospitaladmin:read", "doctor:read","agenthospital:read"])]
    private $updated_at;

    /**
     * @var Collection<int, Doctor>
     */
    #[ORM\ManyToMany(targetEntity: Doctor::class, mappedBy: 'hospital')]
    #[Groups(['hospital:read', "hospitaladmin:read"])]
    private Collection $doctors;

    /**
     * @var Collection<int, AgentHospital>
     */
    #[ORM\OneToMany(targetEntity: AgentHospital::class, mappedBy: 'hospital')]
    private Collection $agentHospitals;

    #[ORM\Column]
    #[Groups(['hospital:read', "hospitaladmin:read", "disponibilite:read",'urgentist:read'])]
    private ?int $infirmiers = null;

    #[ORM\Column]
    #[Groups(['hospital:read', "hospitaladmin:read", "disponibilite:read",'urgentist:read'])]
    private ?int $autres_personnel_de_sante = null;

    #[ORM\ManyToOne(inversedBy: 'hospitals')]
    #[ORM\JoinColumn(nullable: false)]
    #[Groups(['hospital:read', "hospitaladmin:read", "doctor:read", "agenthospital:read", "disponibilite:read",'urgentist:read'])]
    private ?TypeHopital $type_hospital = null;

    /**
     * @var Collection<int, Service>
     */
    #[ORM\ManyToMany(targetEntity: Service::class, inversedBy: 'hospital')]
    #[Groups(['hospital:read', "hospitaladmin:read", "doctor:read", "agenthospital:read", "disponibilite:read",'urgentist:read'])]
    private Collection $services;

    /**
     * @var Collection<int, Disponibilite>
     */
    #[ORM\OneToMany(targetEntity: Disponibilite::class, mappedBy: 'hospital')]
    private Collection $disponibilites;

    /**
     * @var Collection<int, HistoriqueMedical>
     */
    #[ORM\OneToMany(targetEntity: HistoriqueMedical::class, mappedBy: 'hospital')]
    private Collection $historiqueMedicals;

    /**
     * @var Collection<int, Meeting>
     */
    #[ORM\OneToMany(targetEntity: Meeting::class, mappedBy: 'hospital')]
    private Collection $meetings;

    /**
     * @var Collection<int, Urgentist>
     */
    #[ORM\OneToMany(targetEntity: Urgentist::class, mappedBy: 'hospital_id')]
    private Collection $urgentists;

    /**
     * @var Collection<int, Urgency>
     */
    #[ORM\OneToMany(targetEntity: Urgency::class, mappedBy: 'tranfere_a')]
    private Collection $urgencies;

    /**
     * @var Collection<int, Online>
     */
    #[ORM\OneToMany(targetEntity: Online::class, mappedBy: 'hospital_id')]
    private Collection $onlines;

    public function __construct()
    {
        $this->agentHospitals = new ArrayCollection();
        $this->uuid = Uuid::v7()->toString();
        $this->created_at = new \DateTime();
        $this->updated_at = new \DateTime();
        $this->services = new ArrayCollection();
        $this->disponibilites = new ArrayCollection();
        $this->historiqueMedicals = new ArrayCollection();
        $this->meetings = new ArrayCollection();
        $this->urgentists = new ArrayCollection();
        $this->urgencies = new ArrayCollection();
        $this->onlines = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUuid(): ?string
    {
        return $this->uuid;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;
        return $this;
    }

    public function getAddress(): ?string
    {
        return $this->address;
    }

    public function setAddress(string $address): self
    {
        $this->address = $address;
        return $this;
    }

    public function getClientServiceTel(): ?string
    {
        return $this->clientServiceTel;
    }

    public function setClientServiceTel(string $clientServiceTel): self
    {
        $this->clientServiceTel = $clientServiceTel;
        return $this;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;
        return $this;
    }

    public function getWebSite(): ?string
    {
        return $this->webSite;
    }

    public function setWebSite(?string $webSite): self
    {
        $this->webSite = $webSite;
        return $this;
    }

    public function getRegistrationNumber(): ?string
    {
        return $this->registrationNumber;
    }

    public function setRegistrationNumber(string $registrationNumber): self
    {
        $this->registrationNumber = $registrationNumber;
        return $this;
    }

    public function getCeo(): ?string
    {
        return $this->ceo;
    }

    public function setCeo(string $ceo): self
    {
        $this->ceo = $ceo;
        return $this;
    }

    public function getAccreditation(): ?string
    {
        return $this->accreditation;
    }

    public function setAccreditation(string $accreditation): self
    {
        $this->accreditation = $accreditation;
        return $this;
    }

    public function getNiu(): ?string
    {
        return $this->niu;
    }

    public function setNiu(string $niu): self
    {
        $this->niu = $niu;
        return $this;
    }

    public function getRccm(): ?string
    {
        return $this->rccm;
    }

    public function setRccm(string $rccm): self
    {
        $this->rccm = $rccm;
        return $this;
    }

    public function getHasUrgency(): ?bool
    {
        return $this->hasUrgency;
    }

    public function setHasUrgency(bool $hasUrgency): self
    {
        $this->hasUrgency = $hasUrgency;
        return $this;
    }

    public function getHasAmbulance(): ?bool
    {
        return $this->hasAmbulance;
    }

    public function setHasAmbulance(bool $hasAmbulance): self
    {
        $this->hasAmbulance = $hasAmbulance;
        return $this;
    }

    public function getExploitationLisence(): ?string
    {
        return $this->exploitationLisence;
    }

    public function setExploitationLisence(string $exploitationLisence): self
    {
        $this->exploitationLisence = $exploitationLisence;
        return $this;
    }

    public function getAccreditationCertificate(): ?string
    {
        return $this->accreditationCertificate;
    }

    public function setAccreditationCertificate(string $accreditationCertificate): self
    {
        $this->accreditationCertificate = $accreditationCertificate;
        return $this;
    }

    public function getLogo(): ?string
    {
        return $this->logo;
    }

    public function setLogo(?string $logo): self
    {
        $this->logo = $logo;
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

    public function getStatus(): ?Status
    {
        return $this->status;
    }

    public function setStatus(?Status $status): static
    {
        $this->status = $status;

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
            $doctor->addHospital($this);
        }

        return $this;
    }

    public function removeDoctor(Doctor $doctor): static
    {
        if ($this->doctors->removeElement($doctor)) {
            $doctor->removeHospital($this);
        }

        return $this;
    }

    /**
     * @return Collection<int, AgentHospital>
     */
    public function getAgentHospitals(): Collection
    {
        return $this->agentHospitals;
    }

    public function addAgentHospital(AgentHospital $agentHospital): static
    {
        if (!$this->agentHospitals->contains($agentHospital)) {
            $this->agentHospitals->add($agentHospital);
            $agentHospital->setHospital($this);
        }

        return $this;
    }

    public function removeAgentHospital(AgentHospital $agentHospital): static
    {
        if ($this->agentHospitals->removeElement($agentHospital)) {
            // set the owning side to null (unless already changed)
            if ($agentHospital->getHospital() === $this) {
                $agentHospital->setHospital(null);
            }
        }

        return $this;
    }

    public function getInfirmiers(): ?int
    {
        return $this->infirmiers;
    }

    public function setInfirmiers(int $infirmiers): static
    {
        $this->infirmiers = $infirmiers;

        return $this;
    }

    public function getAutresPersonnelDeSante(): ?int
    {
        return $this->autres_personnel_de_sante;
    }

    public function setAutresPersonnelDeSante(int $autres_personnel_de_sante): static
    {
        $this->autres_personnel_de_sante = $autres_personnel_de_sante;

        return $this;
    }

    public function getTypeHospital(): ?TypeHopital
    {
        return $this->type_hospital;
    }

    public function setTypeHospital(?TypeHopital $type_hospital): static
    {
        $this->type_hospital = $type_hospital;

        return $this;
    }

    /**
     * @return Collection<int, Service>
     */
    public function getServices(): Collection
    {
        return $this->services;
    }

    public function addService(Service $service): static
    {
        if (!$this->services->contains($service)) {
            $this->services->add($service);
        }

        return $this;
    }

    public function removeService(Service $service): static
    {
        $this->services->removeElement($service);

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
            $disponibilite->setHospital($this);
        }

        return $this;
    }

    public function removeDisponibilite(Disponibilite $disponibilite): static
    {
        if ($this->disponibilites->removeElement($disponibilite)) {
            // set the owning side to null (unless already changed)
            if ($disponibilite->getHospital() === $this) {
                $disponibilite->setHospital(null);
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

    /**
     * @return Collection<int, Meeting>
     */
    public function getMeetings(): Collection
    {
        return $this->meetings;
    }

    public function addMeeting(Meeting $meeting): static
    {
        if (!$this->meetings->contains($meeting)) {
            $this->meetings->add($meeting);
            $meeting->setHospital($this);
        }

        return $this;
    }

    public function removeMeeting(Meeting $meeting): static
    {
        if ($this->meetings->removeElement($meeting)) {
            // set the owning side to null (unless already changed)
            if ($meeting->getHospital() === $this) {
                $meeting->setHospital(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Urgentist>
     */
    public function getUrgentists(): Collection
    {
        return $this->urgentists;
    }

    public function addUrgentist(Urgentist $urgentist): static
    {
        if (!$this->urgentists->contains($urgentist)) {
            $this->urgentists->add($urgentist);
            $urgentist->setHospitalId($this);
        }

        return $this;
    }

    public function removeUrgentist(Urgentist $urgentist): static
    {
        if ($this->urgentists->removeElement($urgentist)) {
            // set the owning side to null (unless already changed)
            if ($urgentist->getHospitalId() === $this) {
                $urgentist->setHospitalId(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Urgency>
     */
    public function getUrgencies(): Collection
    {
        return $this->urgencies;
    }

    public function addUrgency(Urgency $urgency): static
    {
        if (!$this->urgencies->contains($urgency)) {
            $this->urgencies->add($urgency);
            $urgency->setTranfereA($this);
        }

        return $this;
    }

    public function removeUrgency(Urgency $urgency): static
    {
        if ($this->urgencies->removeElement($urgency)) {
            // set the owning side to null (unless already changed)
            if ($urgency->getTranfereA() === $this) {
                $urgency->setTranfereA(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Online>
     */
    public function getOnlines(): Collection
    {
        return $this->onlines;
    }

    public function addOnline(Online $online): static
    {
        if (!$this->onlines->contains($online)) {
            $this->onlines->add($online);
            $online->setHospitalId($this);
        }

        return $this;
    }

    public function removeOnline(Online $online): static
    {
        if ($this->onlines->removeElement($online)) {
            // set the owning side to null (unless already changed)
            if ($online->getHospitalId() === $this) {
                $online->setHospitalId(null);
            }
        }

        return $this;
    }

}
