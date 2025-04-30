<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use App\Repository\AgentHospitalRepository;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Serializer\Attribute\Groups;

#[ORM\Entity(repositoryClass: AgentHospitalRepository::class)]
class AgentHospital
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(["agent_hospital:read","data_select"])] // Add the appropriate groups here
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'agentHospitals')]
    #[ORM\JoinColumn(nullable: false)]
    #[Groups(["agent_hospital:read","data_select"])] // Add the appropriate groups here
    private ?User $user = null;

    #[ORM\ManyToOne(inversedBy: 'agentHospitals')]
    #[ORM\JoinColumn(nullable: false)]
    #[Groups(["agent_hospital:read","data_select"])] // Add the appropriate groups here
    private ?Hospital $hospital = null;

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
}
