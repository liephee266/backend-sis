<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Attribute\Groups;

#[ORM\Entity]
#[ORM\Table(name: "service")]
class Service
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: "integer")]
    #[Groups(["service:read", "doctor:read", "meeting:read", "consultation:read", "treatment:read", "examination:read", "affiliation:read", "availability:read"])]
    private ?int $id = null;

    #[ORM\Column(type: "string", length: 255, nullable: false)]
    #[Groups(["service:read", "doctor:read", "meeting:read", "consultation:read", "treatment:read", "examination:read", "affiliation:read", "availability:read"])]
    private string $name;

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

}
