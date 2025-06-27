<?php

namespace App\Entity;

use App\Repository\UrgentistRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Uid\Uuid;

#[ORM\Entity(repositoryClass: UrgentistRepository::class)]
class Urgentist
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'urgentists')]
    #[ORM\JoinColumn(nullable: false)]
    #[Groups(['urgentist:read',"urgency:read"])]
    private ?User $user = null;

    #[ORM\ManyToOne(inversedBy: 'urgentists')]
    #[Groups(['urgentist:read',"urgency:read"])]
    private ?Hospital $hospital_id = null;

    #[ORM\Column(length: 255)]
    #[Groups(['urgentist:read',"urgency:read"])]
    private ?string $uuid = null;

    #[ORM\Column(type: "datetime")]
    #[Groups(['urgentist:read',"urgency:read"])]
    private ?\DateTimeInterface $created_at = null;

    #[ORM\Column(type: "datetime")]
    #[Groups(['urgentist:read',"urgency:read"])]
    private ?\DateTimeInterface $updated_at = null;

    /**
     * @var Collection<int, Urgency>
     */
    #[ORM\OneToMany(targetEntity: Urgency::class, mappedBy: 'prise_en_charge')]
    private Collection $urgencies;

    public function __construct()
    {
        $this->urgencies = new ArrayCollection();
        $this->uuid = Uuid::v7()->toString();
        $this->created_at = new \DateTime();
        $this->updated_at = new \DateTime();
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

    public function getHospitalId(): ?Hospital
    {
        return $this->hospital_id;
    }

    public function setHospitalId(?Hospital $hospital_id): static
    {
        $this->hospital_id = $hospital_id;

        return $this;
    }

    /**
     * @return Collection<int, Urgency>
     */
    public function getUrgencies(): Collection
    {
        return $this->urgencies;
    }

    public function addUrgency(Urgency $urgency): static
    {
        if (!$this->urgencies->contains($urgency)) {
            $this->urgencies->add($urgency);
            $urgency->setPriseEnCharge($this);
        }

        return $this;
    }

    public function removeUrgency(Urgency $urgency): static
    {
        if ($this->urgencies->removeElement($urgency)) {
            // set the owning side to null (unless already changed)
            if ($urgency->getPriseEnCharge() === $this) {
                $urgency->setPriseEnCharge(null);
            }
        }

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

    public function getCreatedAt(): ?\DateTimeInterface
    {
        return $this->created_at;
    }

    public function setCreatedAt(\DateTimeInterface $created_at): self
    {
        $this->created_at = $created_at;
        return $this;
    }

    public function getUpdatedAt(): ?\DateTimeInterface
    {
        return $this->updated_at;
    }

    public function setUpdatedAt(\DateTimeInterface $updated_at): static
    {
        $this->updated_at = $updated_at;

        return $this;
    }
}
