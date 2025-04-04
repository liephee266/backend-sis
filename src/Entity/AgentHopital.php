<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Attribute\Groups;

#[ORM\Entity]
#[ORM\Table(name: "agent_hopital")]
class AgentHopital
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: "integer", unique: true)]
    #[Groups(['agent_hopital:read'])]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'agentHopitals')]
    #[Groups(['agent_hopital:read','user:read'])]
    private ?User $user = null;

    // Getters & Setters
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

 
}
