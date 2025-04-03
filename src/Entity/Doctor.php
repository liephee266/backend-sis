<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use App\Entity\User;
use App\Entity\Service;

#[ORM\Entity]
#[ORM\Table(name: "doctor")]
class Doctor
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: "integer", unique: true)]
    private ?int $id = null;

    #[ORM\ManyToOne(targetEntity: User::class)]
    #[ORM\JoinColumn(name: "user_id", referencedColumnName: "id", nullable: false, onDelete: "CASCADE")]
    private ?User $user = null;

    #[ORM\Column(type: "string", length: 255, nullable: false)]
    private ?string $medLisenceNumber = null;

    #[ORM\Column(type: "string", length: 255, nullable: false)]
    private ?string $speciality = null;

    #[ORM\Column(type: "integer", nullable: false)]
    private ?int $experience = null;

    #[ORM\Column(type: "date", nullable: false)]
    private ?\DateTimeInterface $serviceStartingDate = null;

    #[ORM\Column(type: "string", length: 255, nullable: true)]
    private ?string $diplome = null;

    #[ORM\Column(type: "string", length: 255, nullable: true)]
    private ?string $other = null;

    #[ORM\Column(type: "string", length: 255, nullable: false)]
    private ?string $cni = null;

    #[ORM\Column(type: "string", length: 255, nullable: false)]
    private ?string $medicalLisenceCertificate = null;

    #[ORM\Column(type: "string", length: 255, nullable: false)]
    private ?string $cv = null;

    #[ORM\ManyToOne(targetEntity: Service::class)]
    #[ORM\JoinColumn(name: "service_id", referencedColumnName: "id", nullable: false, onDelete: "CASCADE")]
    private ?Service $service = null;

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
}
