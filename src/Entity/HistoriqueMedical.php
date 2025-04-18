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
    private ?Patient $patient = null;

    #[ORM\ManyToOne(inversedBy: 'historiqueMedicals')]
    private ?Consultation $consultation = null;

    #[ORM\ManyToOne(inversedBy: 'historiqueMedicals')]
    private ?Treatment $treatment = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $motifDeLaConsultation = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $recommandations = null;

    #[ORM\ManyToOne(inversedBy: 'historiqueMedicals')]
    private ?Doctor $medecinTraitant = null;

    #[ORM\ManyToOne(inversedBy: 'historiqueMedicals')]
    private ?Hospital $hospital = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $observationsEtDiagnostics = null;

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

    public function getTreatment(): ?Treatment
    {
        return $this->treatment;
    }

    public function setTreatment(?Treatment $treatment): static
    {
        $this->treatment = $treatment;

        return $this;
    }

    public function getMotifDeLaConsultation(): ?string
    {
        return $this->motifDeLaConsultation;
    }

    public function setMotifDeLaConsultation(?string $motifDeLaConsultation): static
    {
        $this->motifDeLaConsultation = $motifDeLaConsultation;

        return $this;
    }

    public function getRecommandations(): ?string
    {
        return $this->recommandations;
    }

    public function setRecommandations(?string $recommandations): static
    {
        $this->recommandations = $recommandations;

        return $this;
    }

    public function getMedecinTraitant(): ?Doctor
    {
        return $this->medecinTraitant;
    }

    public function setMedecinTraitant(?Doctor $medecinTraitant): static
    {
        $this->medecinTraitant = $medecinTraitant;

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

    public function getObservationsEtDiagnostics(): ?string
    {
        return $this->observationsEtDiagnostics;
    }

    public function setObservationsEtDiagnostics(?string $observationsEtDiagnostics): static
    {
        $this->observationsEtDiagnostics = $observationsEtDiagnostics;

        return $this;
    }
}
