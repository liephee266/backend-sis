<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Attribute\Groups;

#[ORM\Entity]
#[ORM\Table(name: "receptionist")]
class Receptionist
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: "integer")]
    #[Groups(['receptionist:read'])]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'receptionist_id')]
    #[ORM\JoinColumn(nullable: false)]
    #[Groups(['receptionist:read'])]
    private ?User $user_id = null;

    // Getters et Setters
    public function getId(): ?int
    {
        return $this->id;
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
