<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Attribute\Groups;

#[ORM\Entity]
#[ORM\Table(name: "content_Message")]
class ContentMessage
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: "integer")]
    #[Groups(["contentmessage:read", "message:read"])]
    private ?int $id = null;

    #[ORM\Column(type: "text")]
    #[Groups(["contentmessage:read", "message:read"])]
    private ?string $msg = null;

    #[ORM\Column(type: "string", length: 255)]
    #[Groups(["contentmessage:read", "message:read"])]
    private ?string $type = null;

    #[ORM\Column(type: "string", length: 255, nullable: true)]
    #[Groups(["contentmessage:read", "message:read"])]
    private ?string $path = null;

    // ✅ Getters & Setters

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getMsg(): ?string
    {
        return $this->msg;
    }

    public function setMsg(string $msg): self
    {
        $this->msg = $msg;
        return $this;
    }

    public function getType(): ?string
    {
        return $this->type;
    }

    public function setType(string $type): self
    {
        $this->type = $type;
        return $this;
    }

    public function getPath(): ?string
    {
        return $this->path;
    }

    public function setPath(?string $path): self
    {
        $this->path = $path;
        return $this;
    }
}
