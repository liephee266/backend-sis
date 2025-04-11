<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Attribute\Groups;

#[ORM\Entity]
#[ORM\Table(name: "treatment")]
class Treatment
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: "integer")]
    #[Groups(["treatment:read"])]
    private ?int $id = null;

    #[ORM\Column(type: "text", nullable: true)]
    #[Groups(["treatment:read"])]
    private ?string $description = null;

    #[ORM\ManyToOne(targetEntity: Consultation::class)]
    #[ORM\JoinColumn(name: "consultation_id", referencedColumnName: "id", nullable: false, onDelete: "CASCADE")]
    #[Groups(["treatment:read"])]
    private ?Consultation $consultation = null;

    #[ORM\Column]
    #[Groups(["treatment:read"])]
    private ?bool $statut = true;

    /**
     * @var Collection<int, DossierMedicale>
     */
    #[ORM\OneToMany(targetEntity: DossierMedicale::class, mappedBy: 'treatment_id')]
    private Collection $dossierMedicales;

    public function __construct()
    {
        $this->dossierMedicales = new ArrayCollection();
    }

    // ✅ Getters & Setters

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

    public function getConsultation(): ?Consultation
    {
        return $this->consultation;
    }

    public function setConsultation(?Consultation $consultation): static
    {
        $this->consultation = $consultation;

        return $this;
    }

    public function isStatut(): ?bool
    {
        return $this->statut;
    }

    public function setStatut(bool $statut): static
    {
        $this->statut = $statut;

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
            $dossierMedicale->setTreatmentId($this);
        }

        return $this;
    }

    public function removeDossierMedicale(DossierMedicale $dossierMedicale): static
    {
        if ($this->dossierMedicales->removeElement($dossierMedicale)) {
            // set the owning side to null (unless already changed)
            if ($dossierMedicale->getTreatmentId() === $this) {
                $dossierMedicale->setTreatmentId(null);
            }
        }

        return $this;
    }
}
