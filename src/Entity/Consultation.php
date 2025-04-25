<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use App\Entity\Patient;
use App\Entity\Doctor;
use App\Entity\Hospital;
use Symfony\Component\Serializer\Attribute\Groups;
use Symfony\Component\Uid\Uuid;

#[ORM\Entity]
#[ORM\Table(name: "consultation")]
class Consultation
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: "integer", unique: true)]
    #[Groups(["data_select","consultation:read", "treatment:read", "examination:read", "DossierMedicale:read","HistoriqueMedical:read","patient:read"])]
    private ?int $id = null;

    #[ORM\ManyToOne(targetEntity: Patient::class)]
    #[ORM\JoinColumn(name: "id_patient", referencedColumnName: "id", nullable: false, onDelete: "CASCADE")]
    #[Groups(["consultation:read", "treatment:read", "examination:read", "DossierMedicale:read", "HistoriqueMedical:read"])]
    private ?Patient $patient = null;

    #[ORM\ManyToOne(targetEntity: Doctor::class)]
    #[ORM\JoinColumn(name: "id_doctor", referencedColumnName: "id", nullable: false, onDelete: "CASCADE")]
    #[Groups(["consultation:read", "treatment:read", "examination:read", "DossierMedicale:read","HistoriqueMedical:read", "patient:read"])]
    private ?Doctor $doctor = null;

    #[ORM\ManyToOne(targetEntity: Hospital::class)]
    #[ORM\JoinColumn(name: "id_hospital", referencedColumnName: "id", nullable: true, onDelete: "SET NULL")]
    #[Groups(["consultation:read", "treatment:read", "examination:read", "DossierMedicale:read", "HistoriqueMedical:read", "patient:read"])]
    private ?Hospital $hospital = null;

    #[ORM\Column(type: "text", nullable: true)]
    #[Groups(["data_select","consultation:read", "treatment:read", "examination:read", "dossier_medicale:read"])]
    private ?string $raison_visite = null;

    #[ORM\Column(type: "text", nullable: true)]
    #[Groups(["consultation:read", "treatment:read", "examination:read", "DossierMedicale:read", "HistoriqueMedical:read", "patient:read"])]
    private ?string $symptoms = null;

    #[ORM\Column(type: "text", nullable: true)]
    #[Groups(["consultation:read", "treatment:read", "examination:read", "dossier_medicale:read"])]
    private ?string $recommandation = null;

    #[ORM\Column(type: "text", nullable: true)]
    #[Groups(["consultation:read", "treatment:read", "examination:read", "DossierMedicale:read", "HistoriqueMedical:read", "patient:read"])]
    private ?string $comment = null;

    #[ORM\Column(type: "date", nullable: true)]
    #[Groups(["consultation:read", "treatment:read", "examination:read", "DossierMedicale:read", "HistoriqueMedical:read", "patient:read"])]
    private ?\DateTimeInterface $dateSymptoms = null;

    /**
     * @var Collection<int, Examination>
     */
    #[ORM\OneToMany(targetEntity: Examination::class, mappedBy: 'consultation')]
    private Collection $examinations;

    /**
     * @var Collection<int, Treatment>
     */
    #[ORM\OneToMany(targetEntity: Treatment::class, mappedBy: 'consultation')]
    private Collection $treatments;

    /**
     * @var Collection<int, DossierMedicale>
     */
    #[ORM\OneToMany(targetEntity: DossierMedicale::class, mappedBy: 'consultation_id')]
    private Collection $dossierMedicales;

    #[ORM\Column(length: 255)]
    #[Groups(["consultation:read", "treatment:read", "examination:read", "dossier_medicale:read"])]
    private ?string $uuid = null;

    #[ORM\Column(type: Types::DATE_MUTABLE)]
    #[Groups(["consultation:read", "treatment:read", "examination:read", "dossier_medicale:read"])]
    private ?\DateTimeInterface $created_at = null;

    #[ORM\Column(type: Types::DATE_MUTABLE)]
    #[Groups(["consultation:read", "treatment:read", "examination:read", "dossier_medicale:read"])]
    private ?\DateTimeInterface $updated_at = null;

    #[ORM\Column(length: 255)]
    #[Groups(["consultation:read", "treatment:read", "examination:read", "dossier_medicale:read"])]
    private ?string $intensité_symptome = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    #[Groups(["consultation:read", "treatment:read", "examination:read", "dossier_medicale:read"])]
    private ?\DateTimeInterface $prochaine_consultation = null;

    #[ORM\Column(length: 255, nullable: true)]
    #[Groups(["consultation:read", "treatment:read", "examination:read", "dossier_medicale:read"])]
    private ?string $test_supplementaire = null;

    #[ORM\Column(length: 255)]
    #[Groups(["consultation:read", "treatment:read", "examination:read", "dossier_medicale:read"])]
    private ?string $diagnostic_preliminaire = null;

    #[ORM\Column(nullable: true)]

    private ?array $antecedents_medicaux = null;

    public function __construct()
    {
        $this->examinations = new ArrayCollection();
        $this->treatments = new ArrayCollection();
        $this->dossierMedicales = new ArrayCollection();
        $this->uuid = Uuid::v7()->toString();
        $this->created_at = new \DateTime();
        $this->updated_at = new \DateTime();
        $this->historiqueMedicals = new ArrayCollection();
        $this->historiqueMedicalsGeneral = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getRaisonVisite(): ?string
    {
        return $this->raison_visite;
    }

    public function setRaisonVisite(?string $raison_visite): self
    {
        $this->raison_visite = $raison_visite;
        return $this;
    }

    public function getSymptoms(): ?string
    {
        return $this->symptoms;
    }

    public function setSymptoms(?string $symptoms): self
    {
        $this->symptoms = $symptoms;
        return $this;
    }

    public function getRecommandation(): ?string
    {
        return $this->recommandation;
    }

    public function setRecommandation(?string $recommandation): self
    {
        $this->recommandation = $recommandation;
        return $this;
    }

    public function getComment(): ?string
    {
        return $this->comment;
    }

    public function setComment(?string $comment): self
    {
        $this->comment = $comment;
        return $this;
    }

    public function getDateSymptoms(): ?\DateTimeInterface
    {
        return $this->dateSymptoms;
    }

    public function setDateSymptoms(?\DateTimeInterface $dateSymptoms): self
    {
        $this->dateSymptoms = $dateSymptoms;
        return $this;
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

    public function getDoctor(): ?Doctor
    {
        return $this->doctor;
    }

    public function setDoctor(?Doctor $doctor): static
    {
        $this->doctor = $doctor;

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

    /**
     * @return Collection<int, Examination>
     */
    public function getExaminations(): Collection
    {
        return $this->examinations;
    }

    public function addExamination(Examination $examination): static
    {
        if (!$this->examinations->contains($examination)) {
            $this->examinations->add($examination);
            $examination->setConsultation($this);
        }

        return $this;
    }

    public function removeExamination(Examination $examination): static
    {
        if ($this->examinations->removeElement($examination)) {
            // set the owning side to null (unless already changed)
            if ($examination->getConsultation() === $this) {
                $examination->setConsultation(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Treatment>
     */
    public function getTreatments(): Collection
    {
        return $this->treatments;
    }

    public function addTreatment(Treatment $treatment): static
    {
        if (!$this->treatments->contains($treatment)) {
            $this->treatments->add($treatment);
            $treatment->setConsultation($this);
        }

        return $this;
    }

    public function removeTreatment(Treatment $treatment): static
    {
        if ($this->treatments->removeElement($treatment)) {
            // set the owning side to null (unless already changed)
            if ($treatment->getConsultation() === $this) {
                $treatment->setConsultation(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, DossierMedicale>
     */
    public function getDossierMedicales(): Collection
    {
        return $this->dossierMedicales;
    }

    public function addDossierMedicale(DossierMedicale $dossierMedicale): static
    {
        if (!$this->dossierMedicales->contains($dossierMedicale)) {
            $this->dossierMedicales->add($dossierMedicale);
            $dossierMedicale->setConsultationId($this);
        }

        return $this;
    }

    public function removeDossierMedicale(DossierMedicale $dossierMedicale): static
    {
        if ($this->dossierMedicales->removeElement($dossierMedicale)) {
            // set the owning side to null (unless already changed)
            if ($dossierMedicale->getConsultationId() === $this) {
                $dossierMedicale->setConsultationId(null);
            }
        }

        return $this;
    }

    public function getUuid(): ?string
    {
        return $this->uuid;
    }

    public function setUuid(string $uuid): static
    {
        $this->uuid = $uuid;
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

    public function getIntensitéSymptome(): ?string
    {
        return $this->intensité_symptome;
    }

    public function setIntensitéSymptome(string $intensité_symptome): static
    {
        $this->intensité_symptome = $intensité_symptome;

        return $this;
    }

    public function getProchaineConsultation(): ?\DateTimeInterface
    {
        return $this->prochaine_consultation;
    }

    public function setProchaineConsultation(?\DateTimeInterface $prochaine_consultation): static
    {
        $this->prochaine_consultation = $prochaine_consultation;

        return $this;
    }

    public function getTestSupplementaire(): ?string
    {
        return $this->test_supplementaire;
    }

    public function setTestSupplementaire(?string $test_supplementaire): static
    {
        $this->test_supplementaire = $test_supplementaire;

        return $this;
    }

    public function getDiagnosticPreliminaire(): ?string
    {
        return $this->diagnostic_preliminaire;
    }

    public function setDiagnosticPreliminaire(string $diagnostic_preliminaire): static
    {
        $this->diagnostic_preliminaire = $diagnostic_preliminaire;

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
}