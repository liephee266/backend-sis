<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Attribute\Groups;
use Symfony\Component\Uid\Uuid;


#[ORM\Entity]
#[ORM\Table(name: "super_admin")]
class SuperAdmin
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: "integer")]
    #[Groups("superadmin:read")]
    private ?int $id = null;

    #[ORM\Column(type: "string", length: 255)]
    #[Groups("superadmin:read")]
    private string $username;

    #[ORM\Column(type: "string", length: 255)]
    #[Groups("superadmin:read")]
    private string $password;

    #[ORM\Column(length: 255)]
    #[Groups("superadmin:read")]
    private ?string $uuid = null;

    public function __construct()
    {
        $this->uuid = Uuid::v7()->toString();
    }

    // ✅ Getters & Setters

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUsername(): string
    {
        return $this->username;
    }

    public function setUsername(string $username): self
    {
        $this->username = $username;
        return $this;
    }

    public function getPassword(): string
    {
        return $this->password;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;
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
