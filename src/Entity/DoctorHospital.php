<?php

namespace App\Entity;

use App\Repository\DoctorHospitalRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: DoctorHospitalRepository::class)]
class DoctorHospital
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'doctorHospitals')]
    private ?Doctor $doctor = null;

    #[ORM\ManyToOne(inversedBy: 'doctorHospitals')]
    private ?Hospital $hospital = null;

    public function getId(): ?int
    {
        return $this->id;
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
}
