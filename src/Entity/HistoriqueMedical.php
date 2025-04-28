<?php

namespace App\Entity;

use App\Repository\HistoriqueMedicalRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Attribute\Groups;

#[ORM\Entity(repositoryClass: HistoriqueMedicalRepository::class)]
class HistoriqueMedical
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups("HistoriqueMedical:read","patient:read","patient:read:restricted")]
    private ?int $id = null;

    /**
     * @var Collection<int, Consultation>
     */
    #[ORM\OneToMany(targetEntity: Consultation::class, mappedBy: 'historiqueMedical')]
    #[Groups("HistoriqueMedical:read","patient:read","patient:read:restricted")]
    private Collection $HistoriqueMedicalGeneral;

    #[ORM\ManyToOne(inversedBy: 'HistoriqueMedical')]
    #[Groups("HistoriqueMedical:read","patient:read","patient:read:restricted")]
    private ?Patient $patient = null;

    public function __construct()
    {
        $this->HistoriqueMedicalGeneral = new ArrayCollection();
    }


    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @return Collection<int, Consultation>
     */
    public function getHistoriqueMedicalGeneral(): Collection
    {
        return $this->HistoriqueMedicalGeneral;
    }

    public function addHistoriqueMedicalGeneral(Consultation $historiqueMedicalGeneral): static
    {
        if (!$this->HistoriqueMedicalGeneral->contains($historiqueMedicalGeneral)) {
            $this->HistoriqueMedicalGeneral->add($historiqueMedicalGeneral);
            $historiqueMedicalGeneral->setHistoriqueMedical($this);
        }

        return $this;
    }

    public function removeHistoriqueMedicalGeneral(Consultation $historiqueMedicalGeneral): static
    {
        if ($this->HistoriqueMedicalGeneral->removeElement($historiqueMedicalGeneral)) {
            // set the owning side to null (unless already changed)
            if ($historiqueMedicalGeneral->getHistoriqueMedical() === $this) {
                $historiqueMedicalGeneral->setHistoriqueMedical(null);
            }
        }

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
    
}
