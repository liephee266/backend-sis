<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: "service")]
class Service
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: "integer")]
    private ?int $id = null;

    #[ORM\Column(type: "string", length: 255, nullable: false)]
    private string $name;

    #[ORM\ManyToOne(targetEntity: Doctor::class)]
    #[ORM\JoinColumn(name: "departement_head", referencedColumnName: "id", nullable: true, onDelete: "SET NULL")]
    private ?Doctor $departmentHead = null;

    // ✅ Getters & Setters

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;
        return $this;
    }

    public function getDepartmentHead(): ?Doctor
    {
        return $this->departmentHead;
    }

    public function setDepartmentHead(?Doctor $departmentHead): self
    {
        $this->departmentHead = $departmentHead;
        return $this;
    }
}
