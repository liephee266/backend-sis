<?php

namespace App\Entity;

use App\Repository\AgentHospitalRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Uid\Uuid;

#[ORM\Entity(repositoryClass: AgentHospitalRepository::class)]
class AgentHospital
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(["agenthospital:read","meeting:read"])]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'agentHospitals')]
    #[ORM\JoinColumn(nullable: false)]
    #[Groups(["agenthospital:read","meeting:read"])]
    private ?User $user = null;

    #[ORM\ManyToOne(inversedBy: 'agentHospitals')]
    #[ORM\JoinColumn(nullable: false)]
    #[Groups(["agenthospital:read","meeting:read"])] 
    private ?Hospital $hospital = null;

    #[ORM\Column(length: 255)]
    #[Groups(["agenthospital:read"])]
    private ?string $uuid = null;

    public function __construct()
    {
        $this->uuid = Uuid::v7()->toString();
    }

    public function getId(): ?int
    {
        return $this->id;
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

    public function getHospital(): ?Hospital
    {
        return $this->hospital;
    }

    public function setHospital(?Hospital $hospital): static
    {
        $this->hospital = $hospital;

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
}
