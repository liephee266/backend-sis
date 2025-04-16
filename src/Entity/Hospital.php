<?php

namespace App\Entity;

use App\Entity\Status;
use App\Entity\Service;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\Entity]
#[ORM\Table(name: "hospital")]
class Hospital
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: "integer", unique: true)]
    #[Groups(["hospital:read", "urgency:read", "consultation:read", "treatment:read",
    "examination:read", "hospitaladmin:read", "affiliation:read", "agenda:read", "dossier_medicale:read"])]
    private ?int $id = null;

    #[ORM\Column(type: "string", length: 255, nullable: false)]
    #[Groups(["hospital:read","urgency:read", "consultation:read", "treatment:read",
    "examination:read", "hospitaladmin:read", "affiliation:read", "agenda:read", "dossier_medicale:read"])]
    private ?string $name = null;

    #[ORM\Column(type: "string", length: 255, nullable: false)]
    #[Groups(["hospital:read", "urgency:read", "consultation:read", "treatment:read",
    "examination:read", "hospitaladmin:read", "affiliation:read", "agenda:read", "dossier_medicale:read"])]
    private ?string $address = null;
    

    #[ORM\Column(type: "string", length: 255, nullable: false)]
    #[Groups(["hospital:read", "urgency:read", "consultation:read", "treatment:read",
    "examination:read", "hospitaladmin:read", "affiliation:read", "agenda:read", "dossier_medicale:read"])]
    private ?string $clientServiceTel = null;

    #[ORM\Column(type: "string", length: 255, nullable: false)]
    #[Groups(['hospital:read', "urgency:read", "consultation:read", "treatment:read",
    "examination:read", "hospitaladmin:read", "affiliation:read", "agenda:read", "dossier_medicale:read"])]
    private ?string $email = null;

    #[ORM\Column(type: "text", nullable: true)]
    #[Groups(['hospital:read', "urgency:read", "consultation:read", "treatment:read",
    "examination:read", "hospitaladmin:read", "affiliation:read", "agenda:read", "dossier_medicale:read"])]
    private ?string $webSite = null;

    #[ORM\Column(type: "string", length: 255, nullable: false)]
    #[Groups(['hospital:read', "hospitaladmin:read"])]
    private ?string $registrationNumber = null;

    #[ORM\Column(type: "string", length: 255, nullable: false)]
    #[Groups(['hospital:read', "hospitaladmin:read"])]
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
    #[Groups(['hospital:read', "urgency:read", "hospitaladmin:read"])]
    private ?bool $hasUrgency = false;

    #[ORM\Column(type: "boolean", nullable: false)]
    #[Groups(['hospital:read', "urgency:read", "hospitaladmin:read"])]
    private ?bool $hasAmbulance = false;

    #[ORM\Column(type: "string", length: 255, nullable: false)]
    #[Groups(['hospital:read', "hospitaladmin:read"])]
    private ?string $exploitationLisence = null;

    #[ORM\Column(type: "string", length: 255, nullable: false)]
    #[Groups(['hospital:read', "hospitaladmin:read"])]
    private ?string $accreditationCertificate = null;

    #[ORM\Column(type: "string", length: 255, nullable: true)]
    #[Groups(['hospital:read', "urgency:read", "consultation:read", "treatment:read",
    "examination:read", "hospitaladmin:read", "affiliation:read", "agenda:read"])]
    private ?string $logo = null;

    #[ORM\Column(nullable: true)]
    private ?bool $isArchived = null;

    #[ORM\ManyToOne(inversedBy: 'hospitals')]
    #[ORM\JoinColumn(nullable: false)]
    #[Groups(['hospital:read', "hospitaladmin:read"])]
    private ?Status $status = null;

    /**
     * @var Collection<int, Doctor>
     */
    #[ORM\ManyToMany(targetEntity: Doctor::class, mappedBy: 'hospital')]
    private Collection $doctors;

    /**
     * @var Collection<int, AgentHospitalHospital>
     */
    #[ORM\OneToMany(targetEntity: AgentHospitalHospital::class, mappedBy: 'hospital')]
    private Collection $user;

    /**
    * @var Collection<int, AdminHospiatlHospital>
    */
    #[ORM\OneToMany(targetEntity: AdminHospitalHospital::class, mappedBy: 'hospital')]
    private Collection $adminHospiatlHospitals;

    public function __construct()
    {
        
    }

    public function getId(): ?int
    {
        return $this->id;
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
     * @return Collection<int, AgentHospitalHospital>
     */
    public function getUser(): Collection
    {
        return $this->user;
    }

    public function addUser(AgentHospitalHospital $user): static
    {
        if (!$this->user->contains($user)) {
            $this->user->add($user);
            $user->setHospital($this);
        }

        return $this;
    }

    public function removeUser(AgentHospitalHospital $user): static
    {
        if ($this->user->removeElement($user)) {
            // set the owning side to null (unless already changed)
            if ($user->getHospital() === $this) {
                $user->setHospital(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, AdminHospiatlHospital>
     */
    public function getAdminHospiatlHospitals(): Collection
    {
        return $this->adminHospiatlHospitals;
    }

    public function addAdminHospiatlHospital(AdminHospitalHospital $adminHospiatlHospital): static
    {
        if (!$this->adminHospiatlHospitals->contains($adminHospiatlHospital)) {
            $this->adminHospiatlHospitals->add($adminHospiatlHospital);
            $adminHospiatlHospital->setHospital($this);
        }

        return $this;
    }

    public function removeAdminHospiatlHospital(AdminHospitalHospital $adminHospiatlHospital): static
    {
        if ($this->adminHospiatlHospitals->removeElement($adminHospiatlHospital)) {
            // set the owning side to null (unless already changed)
            if ($adminHospiatlHospital->getHospital() === $this) {
                $adminHospiatlHospital->setHospital(null);
            }
        }

        return $this;
    }

}
