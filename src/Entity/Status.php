<?php

namespace App\Entity;

use App\Repository\StatusRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Attribute\Groups;

#[ORM\Entity(repositoryClass: StatusRepository::class)]
class Status
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(["data_select",'status:read','hospital:read', 'autorisation:read', "hospitaladmin:read",'urgentist:read'])]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Groups(["data_select",'status:read','hospital:read', 'autorisation:read', "hospitaladmin:read",'urgentist:read'])]
    private ?string $name = null;

    /**
     * @var Collection<int, Hospital>
     */
    #[ORM\OneToMany(targetEntity: Hospital::class, mappedBy: 'status')]
    private Collection $hospitals;

    /**
     * @var Collection<int, Autorisation>
     */
    #[ORM\OneToMany(targetEntity: Autorisation::class, mappedBy: 'status_id')]
    private Collection $autorisations;

    #[ORM\Column(length: 255)]
    #[Groups(["data_select",'status:read','hospital:read', 'autorisation:read', "hospitaladmin:read",'urgentist:read'])]
    private ?string $tech_name = null;

    public function __construct()
    {
        $this->hospitals = new ArrayCollection();
        $this->autorisations = new ArrayCollection();
    }


    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): static
    {
        $this->name = $name;

        return $this;
    }

    /**
     * @return Collection<int, Hospital>
     */
    public function getHospitals(): Collection
    {
        return $this->hospitals;
    }

    public function addHospital(Hospital $hospital): static
    {
        if (!$this->hospitals->contains($hospital)) {
            $this->hospitals->add($hospital);
            $hospital->setStatus($this);
        }

        return $this;
    }

    public function removeHospital(Hospital $hospital): static
    {
        if ($this->hospitals->removeElement($hospital)) {
            // set the owning side to null (unless already changed)
            if ($hospital->getStatus() === $this) {
                $hospital->setStatus(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Autorisation>
     */
    public function getAutorisations(): Collection
    {
        return $this->autorisations;
    }

    public function addAutorisation(Autorisation $autorisation): static
    {
        if (!$this->autorisations->contains($autorisation)) {
            $this->autorisations->add($autorisation);
            $autorisation->setStatusId($this);
        }

        return $this;
    }

    public function removeAutorisation(Autorisation $autorisation): static
    {
        if ($this->autorisations->removeElement($autorisation)) {
            // set the owning side to null (unless already changed)
            if ($autorisation->getStatusId() === $this) {
                $autorisation->setStatusId(null);
            }
        }

        return $this;
    }

    public function getTechName(): ?string
    {
        return $this->tech_name;
    }

    public function setTechName(string $tech_name): static
    {
        $this->tech_name = $tech_name;

        return $this;
    }
}
