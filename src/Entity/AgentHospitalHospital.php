<?php

namespace App\Entity;

use App\Repository\AgentHospitalHospitalRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: AgentHospitalHospitalRepository::class)]
class AgentHospitalHospital
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'agentHospitalHospitals')]
    #[ORM\JoinColumn(nullable: false)]
    private ?AgentHospital $agent_hospital = null;

    #[ORM\ManyToOne(inversedBy: 'agentHospitalHospitals')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Hospital $hospital = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getAgentHospital(): ?AgentHospital
    {
        return $this->agent_hospital;
    }

    public function setAgentHospital(?AgentHospital $agent_hospital): static
    {
        $this->agent_hospital = $agent_hospital;

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
