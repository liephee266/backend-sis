<?php

namespace App\Entity;

use App\Repository\AutorisationRepository;
use DateTime;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Attribute\Groups;

#[ORM\Entity(repositoryClass: AutorisationRepository::class)]
class Autorisation
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(["data_select","autorisation:read"])]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'autorisations')]
    #[ORM\JoinColumn(nullable: false)]
    #[Groups(["autorisation:read"])]
    private ?User $demander_id = null;

    #[ORM\Column(length: 255)]
    private ?string $demander_role = null;

    #[ORM\ManyToOne(inversedBy: 'autorisations')]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $validator_id = null;

    #[ORM\Column(length: 255)]
    private ?string $validator_role = null;

    #[ORM\ManyToOne(inversedBy: 'autorisations')]
    private ?Status $status_id = null;

    #[ORM\Column(length: 255, nullable: true)]
    #[Groups(["data_select","autorisation:read"])]
    private ?string $entity = null;

    #[ORM\Column]
    private ?int $entity_id = null;

    #[ORM\Column(type: "datetime")]
    private ?\DateTimeInterface $created_at = null;

    #[ORM\Column(type: "datetime")]
    private $updated_at;

    #[ORM\Column(nullable: true)]
    private ?int $date_limit = null;

    #[ORM\Column(length: 255)]
    private ?string $type_demande = null;
    

    public function __construct()
    {
        $this->created_at = new \DateTime();
        $this->updated_at = new \DateTime();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getDemanderId(): ?User
    {
        return $this->demander_id;
    }

    public function setDemanderId(?User $demander_id): static
    {
        $this->demander_id = $demander_id;

        return $this;
    }

    public function getDemanderRole(): ?string
    {
        return $this->demander_role;
    }

    public function setDemanderRole(string $demander_role): static
    {
        $this->demander_role = $demander_role;

        return $this;
    }

    public function getValidatorId(): ?User
    {
        return $this->validator_id;
    }

    public function setValidatorId(?User $validator_id): static
    {
        $this->validator_id = $validator_id;

        return $this;
    }

    public function getValidatorRole(): ?string
    {
        return $this->validator_role;
    }

    public function setValidatorRole(string $validator_role): static
    {
        $this->validator_role = $validator_role;

        return $this;
    }

    public function getStatusId(): ?Status
    {
        return $this->status_id;
    }

    public function setStatusId(?Status $status_id): static
    {
        $this->status_id = $status_id;

        return $this;
    }

    public function getEntity(): ?string
    {
        return $this->entity;
    }

    public function setEntity(?string $entity): static
    {
        $this->entity = $entity;

        return $this;
    }

    public function getEntityId(): ?int
    {
        return $this->entity_id;
    }

    public function setEntityId(int $entity_id): static
    {
        $this->entity_id = $entity_id;

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

    public function getDateLimit(): ?int
    {
        return $this->date_limit;
    }

    public function setDateLimit(?int $date_limit): static
    {
        $this->date_limit = $date_limit;

        return $this;
    }

    public function getTypeDemande(): ?string
    {
        return $this->type_demande;
    }

    public function setTypeDemande(string $type_demande): static
    {
        $this->type_demande = $type_demande;

        return $this;
    } 
}