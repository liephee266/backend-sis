<?php

namespace App\Entity;

use App\Repository\HistoriqueMedicalRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Attribute\Groups;

#[ORM\Entity(repositoryClass: HistoriqueMedicalRepository::class)]
class HistoriqueMedical
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups("HistoriqueMedical:read")]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'historiqueMedicalsGeneral')]
    #[Groups("HistoriqueMedical:read")]
    private ?Consultation $historiqueMedicalGeneral = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getHistoriqueMedicalGeneral(): ?Consultation
    {
        return $this->historiqueMedicalGeneral;
    }

    public function setHistoriqueMedicalGeneral(?Consultation $historiqueMedicalGeneral): static
    {
        $this->historiqueMedicalGeneral = $historiqueMedicalGeneral;

        return $this;
    }

    
}
