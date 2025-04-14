<?php

namespace App\Entity;

use App\Repository\AgentHospitalRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: AgentHospitalRepository::class)]
class AgentHospital
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'agentHospitals')]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $user = null;

    /**
     * @var Collection<int, AgentHospitalHospital>
     */
    #[ORM\OneToMany(targetEntity: AgentHospitalHospital::class, mappedBy: 'agent_hospital')]
    private Collection $agentHospitalHospitals;

    public function __construct()
    {
        $this->agentHospitalHospitals = new ArrayCollection();
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

    /**
     * @return Collection<int, AgentHospitalHospital>
     */
    public function getAgentHospitalHospitals(): Collection
    {
        return $this->agentHospitalHospitals;
    }

    public function addAgentHospitalHospital(AgentHospitalHospital $agentHospitalHospital): static
    {
        if (!$this->agentHospitalHospitals->contains($agentHospitalHospital)) {
            $this->agentHospitalHospitals->add($agentHospitalHospital);
            $agentHospitalHospital->setAgentHospital($this);
        }

        return $this;
    }

    public function removeAgentHospitalHospital(AgentHospitalHospital $agentHospitalHospital): static
    {
        if ($this->agentHospitalHospitals->removeElement($agentHospitalHospital)) {
            // set the owning side to null (unless already changed)
            if ($agentHospitalHospital->getAgentHospital() === $this) {
                $agentHospitalHospital->setAgentHospital(null);
            }
        }

        return $this;
    }
}
