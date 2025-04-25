<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use App\Entity\User;
use Symfony\Component\Serializer\Attribute\Groups;

#[ORM\Entity]
#[ORM\Table(name: "patient")]
class Patient
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: "integer", unique: true)]
    #[Groups(["patient:read", "meeting:read", "urgency:read", "consultation:read", "treatment:read", "examination:read", "DossierMedicale:read","HistoriqueMedical:read","patient:read"])]
    private ?int $id = null;

    #[ORM\ManyToOne(targetEntity: User::class)]
    #[ORM\JoinColumn(name: "user_id", referencedColumnName: "id", nullable: false, onDelete: "CASCADE")]
    #[Groups(["patient:read", "meeting:read", "urgency:read", "consultation:read", "treatment:read", "examination:read", "DossierMedicale:read","HistoriqueMedical:read","patient:read:restricted"])]
    private ?User $user = null;

    #[ORM\ManyToOne(targetEntity: User::class)]
    #[ORM\JoinColumn(name: "tutor_id", referencedColumnName: "id", nullable: true, onDelete: "SET NULL")]
    #[Groups(["patient:read", "meeting:read", "urgency:read", "consultation:read", "treatment:read", "examination:read", "DossierMedicale:read","patient:read:restricted"])]
    private ?User $tutor = null;
    

    /**
     * @var Collection<int, Meeting>
     */
    #[ORM\OneToMany(targetEntity: Meeting::class, mappedBy: 'patient_id')]
    private Collection $meeting_id;

    /**
     * @var Collection<int, Consultation>
     */
    #[ORM\OneToMany(targetEntity: Consultation::class, mappedBy: 'patient')]
    private Collection $consultations;

    /**
     * @var Collection<int, DossierMedicale>
     */
    #[ORM\OneToMany(targetEntity: DossierMedicale::class, mappedBy: 'patient_id')]
    #[Groups(["patient:read"])]
    private Collection $dossierMedicales;

    #[ORM\Column]
    #[Groups(["patient:read", "meeting:read", "urgency:read", "consultation:read", "treatment:read", "examination:read", "dossier_medicale:read"])]
    private ?int $poids = null;

    #[ORM\Column(length: 255)]
    #[Groups(["patient:read", "meeting:read", "urgency:read", "consultation:read", "treatment:read", "examination:read", "dossier_medicale:read"])]
    private ?string $groupe_sanguins = null;

    #[ORM\Column(length: 255)]
    #[Groups(["patient:read", "meeting:read", "urgency:read", "consultation:read", "treatment:read", "examination:read", "dossier_medicale:read"])]
    private ?string $taille = null;

    #[ORM\Column]

    private ?bool $signaler_comme_decedé = null;

    #[ORM\Column(length: 255)]
    #[Groups(["patient:read", "meeting:read", "urgency:read", "consultation:read", "treatment:read", "examination:read", "dossier_medicale:read"])]
    private ?string $nom_urgence = null;

    #[ORM\Column(length: 255)]
    #[Groups(["patient:read", "meeting:read", "urgency:read", "consultation:read", "treatment:read", "examination:read", "dossier_medicale:read"])]
    private ?string $adresse_urgence = null;

    #[ORM\Column(length: 255)]
    #[Groups(["patient:read", "meeting:read", "urgency:read", "consultation:read", "treatment:read", "examination:read", "dossier_medicale:read"])]
    private ?string $numero_urgence = null;

    /**
     * @var Collection<int, HistoriqueMedical>
     */
    #[ORM\OneToMany(targetEntity: HistoriqueMedical::class, mappedBy: 'patient')]
    private Collection $historiqueMedicals;

    public function __construct()
    {
        $this->meeting_id = new ArrayCollection();
        $this->consultations = new ArrayCollection();
        $this->dossierMedicales = new ArrayCollection();
        $this->historiqueMedicals = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
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
            $consultation->setPatient($this);
        }

        return $this;
    }

    public function removeConsultation(Consultation $consultation): static
    {
        if ($this->consultations->removeElement($consultation)) {
            // set the owning side to null (unless already changed)
            if ($consultation->getPatient() === $this) {
                $consultation->setPatient(null);
            }
        }

        return $this;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): static
    {
        $this->user = $user;

        return $this;
    }

    public function getTutor(): ?User
    {
        return $this->tutor;
    }

    public function setTutor(?User $tutor): static
    {
        $this->tutor = $tutor;

        return $this;
    }

    /**
     * @return Collection<int, Meeting>
     */
    public function getMeetingId(): Collection
    {
        return $this->meeting_id;
    }

    public function addMeetingId(Meeting $meetingId): static
    {
        if (!$this->meeting_id->contains($meetingId)) {
            $this->meeting_id->add($meetingId);
            $meetingId->setPatientId($this);
        }

        return $this;
    }

    public function removeMeetingId(Meeting $meetingId): static
    {
        if ($this->meeting_id->removeElement($meetingId)) {
            // set the owning side to null (unless already changed)
            if ($meetingId->getPatientId() === $this) {
                $meetingId->setPatientId(null);
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
            $dossierMedicale->setPatientId($this);
        }

        return $this;
    }

    public function removeDossierMedicale(DossierMedicale $dossierMedicale): static
    {
        if ($this->dossierMedicales->removeElement($dossierMedicale)) {
            // set the owning side to null (unless already changed)
            if ($dossierMedicale->getPatientId() === $this) {
                $dossierMedicale->setPatientId(null);
            }
        }

        return $this;
    }

    public function getPoids(): ?int
    {
        return $this->poids;
    }

    public function setPoids(int $poids): static
    {
        $this->poids = $poids;

        return $this;
    }

    public function getGroupeSanguins(): ?string
    {
        return $this->groupe_sanguins;
    }

    public function setGroupeSanguins(string $groupe_sanguins): static
    {
        $this->groupe_sanguins = $groupe_sanguins;

        return $this;
    }

    public function getTaille(): ?string
    {
        return $this->taille;
    }

    public function setTaille(string $taille): static
    {
        $this->taille = $taille;

        return $this;
    }

    public function isSignalerCommeDecedé(): ?bool
    {
        return $this->signaler_comme_decedé;
    }

    public function setSignalerCommeDecedé(bool $signaler_comme_decedé): static
    {
        $this->signaler_comme_decedé = $signaler_comme_decedé;

        return $this;
    }

    public function getNomUrgence(): ?string
    {
        return $this->nom_urgence;
    }

    public function setNomUrgence(string $nom_urgence): static
    {
        $this->nom_urgence = $nom_urgence;

        return $this;
    }

    public function getAdresseUrgence(): ?string
    {
        return $this->adresse_urgence;
    }

    public function setAdresseUrgence(string $adresse_urgence): static
    {
        $this->adresse_urgence = $adresse_urgence;

        return $this;
    }

    public function getNumeroUrgence(): ?string
    {
        return $this->numero_urgence;
    }

    public function setNumeroUrgence(string $numero_urgence): static
    {
        $this->numero_urgence = $numero_urgence;

        return $this;
    }

    /**
     * @return Collection<int, HistoriqueMedical>
     */
    public function getHistoriqueMedicals(): Collection
    {
        return $this->historiqueMedicals;
    }
}
