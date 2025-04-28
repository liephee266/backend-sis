<?php

namespace App\Entity;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use App\Repository\DossierMedicaleRepository;
use Symfony\Component\Serializer\Attribute\Groups;
use Symfony\Component\Uid\Uuid;

#[ORM\Entity(repositoryClass: DossierMedicaleRepository::class)]
class DossierMedicale
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups("dossier_medicale:read","patient:read")]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'dossierMedicales')]
    #[Groups(["data_select","dossier_medicale:read","consultation:read","patient:read"])]
    private ?Consultation $consultation_id = null;

    #[ORM\ManyToOne(inversedBy: 'dossierMedicales')]
    #[Groups("dossier_medicale:read","patient:read","urgency:read")]
    private ?Treatment $treatment_id = null;

    #[ORM\ManyToOne(inversedBy: 'dossierMedicales')]
    #[Groups("dossier_medicale:read","urgency:read","patient:read")]
    private ?Patient $patient_id = null;

    #[ORM\Column(type: 'json', nullable: true)]
    #[Groups("dossier_medicale:read","urgency:read","patient:read")]
    private ?array $medications_actuelles = null;

    #[ORM\Column(type: 'json', nullable: true)]
    #[Groups("dossier_medicale:read","urgency:read","patient:read")]
    private ?array $antecedents_familiaux = null;

    #[ORM\Column(type: 'json',nullable: true)]
    private ?array $access = null;

    #[ORM\Column(nullable: true)]
    #[Groups("dossier_medicale:read","urgency:read","patient:read")]
    private ?array $habitude_vie = null;

    #[ORM\Column(type: Types::DATE_MUTABLE)]
    #[Groups("dossier_medicale:read","patient:read")]
    private ?\DateTimeInterface $created_at = null;

    #[ORM\Column(type: Types::DATE_MUTABLE)]
    #[Groups("dossier_medicale:read")]
    private ?\DateTimeInterface $updated_at = null;

    #[ORM\Column(length: 255)]
    #[Groups("dossier_medicale:read","patient:read")]
    private ?string $uuid = null;

    public function __construct()
    {
        $this->uuid = Uuid::v7()->toString();
        $this->created_at = new \DateTime();
        $this->updated_at = new \DateTime();
    }

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

    public function getHabitudeVie(): ?array
    {
        return $this->habitude_vie;
    }

    public function setHabitudeVie(?array $habitude_vie): static
    {
        $this->habitude_vie = $habitude_vie;

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

    public function getCreatedAt(): ?\DateTimeInterface
    {
        return $this->created_at;
    }

    public function setCreatedAt(\DateTimeInterface $created_at): static
    {
        $this->created_at = $created_at;

        return $this;
    }

    public function getUpdatedAt(): ?\DateTimeInterface
    {
        return $this->updated_at;
    }

    public function setUpdatedAt(\DateTimeInterface $updated_at): static
    {
        $this->updated_at = $updated_at;

        return $this;
    }

    public function getUuid(): ?String
    {
        return $this->uuid;
    }

    public function setUuid(String $uuid): static
    {
        $this->uuid = $uuid;
        return $this;
    }
}
