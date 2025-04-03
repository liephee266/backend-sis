<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use App\Entity\Service;

#[ORM\Entity]
#[ORM\Table(name: "hospital")]
class Hospital
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: "integer", unique: true)]
    private ?int $id = null;

    #[ORM\Column(type: "string", length: 255, nullable: false)]
    private ?string $name = null;

    #[ORM\Column(type: "string", length: 255, nullable: false)]
    private ?string $address = null;

    #[ORM\Column(type: "string", length: 255, nullable: false)]
    private ?string $clientServiceTel = null;

    #[ORM\Column(type: "string", length: 255, nullable: false)]
    private ?string $email = null;

    #[ORM\Column(type: "text", nullable: true)]
    private ?string $webSite = null;

    #[ORM\Column(type: "string", length: 255, nullable: false)]
    private ?string $registrationNumber = null;

    #[ORM\Column(type: "string", length: 255, nullable: false)]
    private ?string $ceo = null;

    #[ORM\ManyToOne(targetEntity: Service::class)]
    #[ORM\JoinColumn(name: "service_id", referencedColumnName: "id", nullable: true, onDelete: "SET NULL")]
    private ?Service $service = null;

    #[ORM\Column(type: "string", length: 255, nullable: false)]
    private ?string $accreditation = null;

    #[ORM\Column(type: "string", length: 255, nullable: false)]
    private ?string $niu = null;

    #[ORM\Column(type: "string", length: 255, nullable: false)]
    private ?string $rccm = null;

    #[ORM\Column(type: "boolean", nullable: false)]
    private ?bool $hasUrgency = false;

    #[ORM\Column(type: "boolean", nullable: false)]
    private ?bool $hasAmbulance = false;

    #[ORM\Column(type: "string", length: 255, nullable: false)]
    private ?string $exploitationLisence = null;

    #[ORM\Column(type: "string", length: 255, nullable: false)]
    private ?string $accreditationCertificate = null;

    #[ORM\Column(type: "string", length: 255, nullable: true)]
    private ?string $logo = null;

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

    public function getService(): ?Service
    {
        return $this->service;
    }

    public function setService(?Service $service): self
    {
        $this->service = $service;
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
}
