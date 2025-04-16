<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use App\Repository\DossierMedicaleRepository;
use Symfony\Component\Serializer\Attribute\Groups;

#[ORM\Entity(repositoryClass: DossierMedicaleRepository::class)]
class DossierMedicale
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups("dossier_medicale:read")]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'dossierMedicales')]
    #[Groups(["data_select","dossier_medicale:read"])]
    private ?Consultation $consultation_id = null;

    #[ORM\ManyToOne(inversedBy: 'dossierMedicales')]
    #[Groups("dossier_medicale:read")]
    private ?Treatment $treatment_id = null;

    #[ORM\ManyToOne(inversedBy: 'dossierMedicales')]
    #[Groups("dossier_medicale:read","urgency:read")]
    private ?Patient $patient_id = null;

    #[ORM\Column(nullable: true)]
    #[Groups("dossier_medicale:read","urgency:read")]
    private ?array $antecedents_medicaux = null;

    #[ORM\Column(nullable: true)]
    #[Groups("dossier_medicale:read")]
    private ?array $medications_actuelles = null;

    #[ORM\Column(nullable: true)]
    #[Groups("dossier_medicale:read")]
    private ?array $antecedents_familiaux = null;

    #[ORM\Column(nullable: true)]
    private ?array $access = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getConsultationId(): ?Consultation
    {
        return $this->consultation_id;
    }

    public function setConsultationId(?Consultation $consultation_id): static
    {
        $this->consultation_id = $consultation_id;

        return $this;
    }

    public function getTreatmentId(): ?Treatment
    {
        return $this->treatment_id;
    }

    public function setTreatmentId(?Treatment $treatment_id): static
    {
        $this->treatment_id = $treatment_id;

        return $this;
    }

    public function getPatientId(): ?Patient
    {
        return $this->patient_id;
    }

    public function setPatientId(?Patient $patient_id): static
    {
        $this->patient_id = $patient_id;

        return $this;
    }

    public function getAntecedentsMedicaux(): ?array
    {
        return $this->antecedents_medicaux;
    }

    public function setAntecedentsMedicaux(?array $antecedents_medicaux): static
    {
        $this->antecedents_medicaux = $antecedents_medicaux;

        return $this;
    }

    public function getMedicationsActuelles(): ?array
    {
        return $this->medications_actuelles;
    }

    public function setMedicationsActuelles(?array $medications_actuelles): static
    {
        $this->medications_actuelles = $medications_actuelles;

        return $this;
    }

    public function getAntecedentsFamiliaux(): ?array
    {
        return $this->antecedents_familiaux;
    }

    public function setAntecedentsFamiliaux(?array $antecedents_familiaux): static
    {
        $this->antecedents_familiaux = $antecedents_familiaux;

        return $this;
    }

    public function getAccess(): ?array
    {
        return $this->access;
    }

    public function setAccess(?array $access): static
    {
        $this->access = $access;

        return $this;
    }
}
