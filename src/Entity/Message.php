<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\DBAL\Types\Types;
use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\Entity]
#[ORM\Table(name: "message")]
class Message
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: "integer")]
    #[Groups(["message:read"])]
    private ?int $id = null;

    #[ORM\ManyToOne(targetEntity: User::class)]
    #[ORM\JoinColumn(name: "sender", referencedColumnName: "id", nullable: false, onDelete: "CASCADE")]
    #[Groups(["message:read"])]
    private ?User $sender = null;

    #[ORM\ManyToOne(targetEntity: User::class)]
    #[ORM\JoinColumn(name: "receiver", referencedColumnName: "id", nullable: false, onDelete: "CASCADE")]
    #[Groups(["message:read"])]
    private ?User $receiver = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: false)]
    #[Groups(["message:read"])]
    private ?\DateTimeInterface $date = null;

    #[ORM\ManyToOne(targetEntity: ContentMessage::class)]
    #[ORM\JoinColumn(name: "content_msg_id", referencedColumnName: "id", nullable: false)]
    #[Groups(["message:read"])]
    private ?ContentMessage $contentMsg = null;

    #[ORM\ManyToOne(targetEntity: State::class)]
    #[ORM\JoinColumn(name: "state_id", referencedColumnName: "id", nullable: false)]
    #[Groups(["message:read"])]
    private ?State $state = null;

    // ✅ Getters & Setters

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getSender(): ?User
    {
        return $this->sender;
    }

    public function setSender(?User $sender): self
    {
        $this->sender = $sender;
        return $this;
    }

    public function getReceiver(): ?User
    {
        return $this->receiver;
    }

    public function setReceiver(?User $receiver): self
    {
        $this->receiver = $receiver;
        return $this;
    }

    public function getDate(): ?\DateTimeInterface
    {
        return $this->date;
    }

    public function setDate(\DateTimeInterface $date): self
    {
        $this->date = $date;
        return $this;
    }

    public function getContentMsg(): ?ContentMessage
    {
        return $this->contentMsg;
    }

    public function setContentMsg(?ContentMessage $contentMsg): self
    {
        $this->contentMsg = $contentMsg;
        return $this;
    }

    public function getState(): ?State
    {
        return $this->state;
    }

    public function setState(?State $state): self
    {
        $this->state = $state;
        return $this;
    }
}
