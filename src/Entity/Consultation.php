<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use App\Entity\Patient;
use App\Entity\Doctor;
use App\Entity\Hospital;
use Symfony\Component\Serializer\Attribute\Groups;

#[ORM\Entity]
#[ORM\Table(name: "consultation")]
class Consultation
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: "integer", unique: true)]
    #[Groups(["consultation:read", "treatment:read", "examination:read"])]
    private ?int $id = null;

    #[ORM\ManyToOne(targetEntity: Patient::class)]
    #[ORM\JoinColumn(name: "id_patient", referencedColumnName: "id", nullable: false, onDelete: "CASCADE")]
    #[Groups(["consultation:read", "treatment:read", "examination:read"])]
    private ?Patient $patient = null;

    #[ORM\ManyToOne(targetEntity: Doctor::class)]
    #[ORM\JoinColumn(name: "id_doctor", referencedColumnName: "id", nullable: false, onDelete: "CASCADE")]
    #[Groups(["consultation:read", "treatment:read", "examination:read"])]
    private ?Doctor $doctor = null;

    #[ORM\ManyToOne(targetEntity: Hospital::class)]
    #[ORM\JoinColumn(name: "id_hospital", referencedColumnName: "id", nullable: true, onDelete: "SET NULL")]
    #[Groups(["consultation:read", "treatment:read", "examination:read"])]
    private ?Hospital $hospital = null;

    #[ORM\Column(type: "text", nullable: true)]
    #[Groups(["consultation:read", "treatment:read", "examination:read"])]
    private ?string $description = null;

    #[ORM\Column(type: "text", nullable: true)]
    #[Groups(["consultation:read", "treatment:read", "examination:read"])]
    private ?string $symptoms = null;

    #[ORM\Column(type: "text", nullable: true)]
    #[Groups(["consultation:read", "treatment:read", "examination:read"])]
    private ?string $prescription = null;

    #[ORM\Column(type: "text", nullable: true)]
    #[Groups(["consultation:read", "treatment:read", "examination:read"])]
    private ?string $diagnostic = null;

    #[ORM\Column(type: "text", nullable: true)]
    #[Groups(["consultation:read", "treatment:read", "examination:read"])]
    private ?string $recommendation = null;

    #[ORM\Column(type: "text", nullable: true)]
    #[Groups(["consultation:read", "treatment:read", "examination:read"])]
    private ?string $comment = null;

    #[ORM\Column(type: "date", nullable: true)]
    #[Groups(["consultation:read", "treatment:read", "examination:read"])]
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

    public function __construct()
    {
        $this->examinations = new ArrayCollection();
        $this->treatments = new ArrayCollection();
        $this->dossierMedicales = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): self
    {
        $this->description = $description;
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

    public function getPrescription(): ?string
    {
        return $this->prescription;
    }

    public function setPrescription(?string $prescription): self
    {
        $this->prescription = $prescription;
        return $this;
    }

    public function getDiagnostic(): ?string
    {
        return $this->diagnostic;
    }

    public function setDiagnostic(?string $diagnostic): self
    {
        $this->diagnostic = $diagnostic;
        return $this;
    }

    public function getRecommendation(): ?string
    {
        return $this->recommendation;
    }

    public function setRecommendation(?string $recommendation): self
    {
        $this->recommendation = $recommendation;
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
}
