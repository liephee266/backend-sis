<?php

namespace App\Entity;

use Symfony\Component\Uid\Uuid;
use Doctrine\ORM\Mapping as ORM;
use App\Repository\OnlineRepository;
use phpDocumentor\Reflection\Types\Nullable;
use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\Entity(repositoryClass: OnlineRepository::class)]
class Online
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups('online:read')]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'onlines')]
    #[Groups('online:read')]
    private ?Hospital $hospital_id = null;

    #[ORM\Column(length: 255)]
    #[Groups('online:read')]
    private ?string $uuid = null;

    #[ORM\Column]
    #[Groups('online:read')]
    private ?bool $value = null;

    #[ORM\Column(type: "datetime")]
    #[Groups('online:read')]
    private $created_at;

    #[ORM\Column(type: "datetime")]
    #[Groups('online:read')]
    private $updated_at;

    #[ORM\ManyToOne(inversedBy: 'online')]
    #[Groups('online:read')]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $user_id = null;

    public function __construct()
    {
        $this->uuid = Uuid::v7()->toString();
        $this->created_at = new \DateTime();
        $this->updated_at = new \DateTime();
    }

    public function getId(): ?int
    {
        return $this->id;
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

    public function getUuid(): ?string
    {
        return $this->uuid;
    }

    public function setUuid(string $uuid): static
    {
        $this->uuid = $uuid;
        return $this;
    }

    public function isValue(): ?bool
    {
        return $this->value;
    }

    public function setValue(bool $value): static
    {
        $this->value = $value;

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

    public function setUpdatedAt(\DateTimeInterface $updated_at): self
    {
        $this->updated_at = $updated_at;
        return $this;
    }

    public function getUserId(): ?User
    {
        return $this->user_id;
    }

    public function setUserId(?User $user_id): static
    {
        $this->user_id = $user_id;

        return $this;
    }
}
