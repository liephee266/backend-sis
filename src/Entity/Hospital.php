<?php

namespace App\Entity;

use App\Entity\Service;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Serializer\Attribute\Groups;

#[ORM\Entity]
#[ORM\Table(name: "hospital")]
class Hospital
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: "integer", unique: true)]
    #[Groups(['hospital:read', 'affiliation:read', 'doctor:read','consultation:read','hospital_admin:read'])]
    private ?int $id = null;

    #[ORM\Column(type: "string", length: 255, nullable: false)]
    #[Groups(['hospital:read'])]
    private ?string $name = null;

    #[ORM\Column(type: "string", length: 255, nullable: false)]
    #[Groups(['hospital:read'])]
    private ?string $address = null;

    #[ORM\Column(type: "string", length: 255, nullable: false)]
    #[Groups(['hospital:read'])]
    private ?string $clientServiceTel = null;

    #[ORM\Column(type: "string", length: 255, nullable: false)]
    #[Groups(['hospital:read'])]
    private ?string $email = null;

    #[ORM\Column(type: "text", nullable: true)]
    #[Groups(['hospital:read'])]
    private ?string $webSite = null;

    #[ORM\Column(type: "string", length: 255, nullable: false)]
    #[Groups(['hospital:read'])]
    private ?string $registrationNumber = null;

    #[ORM\Column(type: "string", length: 255, nullable: false)]
    #[Groups(['hospital:read'])]
    private ?string $ceo = null;

    #[ORM\Column(type: "string", length: 255, nullable: false)]
    #[Groups(['hospital:read'])]
    private ?string $accreditation = null;

    #[ORM\Column(type: "string", length: 255, nullable: false)]
    #[Groups(['hospital:read'])]
    private ?string $niu = null;

    #[ORM\Column(type: "string", length: 255, nullable: false)]
    #[Groups(['hospital:read'])]
    private ?string $rccm = null;

    #[ORM\Column(type: "boolean", nullable: false)]
    #[Groups(['hospital:read'])]
    private ?bool $hasUrgency = false;

    #[ORM\Column(type: "boolean", nullable: false)]
    #[Groups(['hospital:read'])]
    private ?bool $hasAmbulance = false;

    #[ORM\Column(type: "string", length: 255, nullable: false)]
    #[Groups(['hospital:read'])]
    private ?string $exploitationLisence = null;

    #[ORM\Column(type: "string", length: 255, nullable: false)]
    #[Groups(['hospital:read'])]
    private ?string $accreditationCertificate = null;

    #[ORM\Column(type: "string", length: 255, nullable: true)]
    #[Groups(['hospital:read'])]
    private ?string $logo = null;

    /**
     * @var Collection<int, Affiliation>
     */
    #[ORM\OneToMany(targetEntity: Affiliation::class, mappedBy: 'hospital')]
    private Collection $affiliations;

    /**
     * @var Collection<int, Agenda>
     */
    #[ORM\OneToMany(targetEntity: Agenda::class, mappedBy: 'hospital')]
    private Collection $agenda;

    /**
     * @var Collection<int, Consultation>
     */
    #[ORM\OneToMany(targetEntity: Consultation::class, mappedBy: 'hospital')]
    private Collection $consultations;

    /**
     * @var Collection<int, HospitalAdmin>
     */
    #[ORM\OneToMany(targetEntity: HospitalAdmin::class, mappedBy: 'hospital')]
    private Collection $hospitalAdmins;

    public function __construct()
    {
        $this->affiliations = new ArrayCollection();
        $this->agenda = new ArrayCollection();
        $this->consultations = new ArrayCollection();
        $this->hospitalAdmins = new ArrayCollection();
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
            $affiliation->setHospital($this);
        }

        return $this;
    }

    public function removeAffiliation(Affiliation $affiliation): static
    {
        if ($this->affiliations->removeElement($affiliation)) {
            // set the owning side to null (unless already changed)
            if ($affiliation->getHospital() === $this) {
                $affiliation->setHospital(null);
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
            $agenda->setHospital($this);
        }

        return $this;
    }

    public function removeAgenda(Agenda $agenda): static
    {
        if ($this->agenda->removeElement($agenda)) {
            // set the owning side to null (unless already changed)
            if ($agenda->getHospital() === $this) {
                $agenda->setHospital(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Consultation>
     */
    public function getConsultations(): Collection
    {
        return $this->consultations;
    }

    public function addConsultation(Consultation $consultation): static
    {
        if (!$this->consultations->contains($consultation)) {
            $this->consultations->add($consultation);
            $consultation->setHospital($this);
        }

        return $this;
    }

    public function removeConsultation(Consultation $consultation): static
    {
        if ($this->consultations->removeElement($consultation)) {
            // set the owning side to null (unless already changed)
            if ($consultation->getHospital() === $this) {
                $consultation->setHospital(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, HospitalAdmin>
     */
    public function getHospitalAdmins(): Collection
    {
        return $this->hospitalAdmins;
    }

    public function addHospitalAdmin(HospitalAdmin $hospitalAdmin): static
    {
        if (!$this->hospitalAdmins->contains($hospitalAdmin)) {
            $this->hospitalAdmins->add($hospitalAdmin);
            $hospitalAdmin->setHospital($this);
        }

        return $this;
    }

    public function removeHospitalAdmin(HospitalAdmin $hospitalAdmin): static
    {
        if ($this->hospitalAdmins->removeElement($hospitalAdmin)) {
            // set the owning side to null (unless already changed)
            if ($hospitalAdmin->getHospital() === $this) {
                $hospitalAdmin->setHospital(null);
            }
        }

        return $this;
    }
}
