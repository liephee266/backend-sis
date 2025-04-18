<?php

namespace App\Entity;

use App\Repository\HistoriqueMedicalRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: HistoriqueMedicalRepository::class)]
class HistoriqueMedical
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'historiqueMedicals')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Patient $patient = null;

    #[ORM\ManyToOne(inversedBy: 'historiqueMedicals')]
    private ?Consultation $consultation = null;

    #[ORM\ManyToOne(inversedBy: 'historiqueMedicals')]
    private ?DossierMedicale $dossierMedical = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getPatient(): ?Patient
    {
        return $this->patient;
    }

    public function setPatient(?Patient $patient): static
    {
        $this->patient = $patient;

        return $this;
    }

    public function getConsultation(): ?Consultation
    {
        return $this->consultation;
    }

    public function setConsultation(?Consultation $consultation): static
    {
        $this->consultation = $consultation;

        return $this;
    }

    public function getDossierMedical(): ?DossierMedicale
    {
        return $this->dossierMedical;
    }

    public function setDossierMedical(?DossierMedicale $dossierMedical): static
    {
        $this->dossierMedical = $dossierMedical;

        return $this;
    }
}
