<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\DBAL\Types\Types;
use Symfony\Component\Serializer\Attribute\Groups;
use Symfony\Component\Uid\Uuid;

#[ORM\Entity]
class Message
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: "integer")]
    #[Groups(["data_select","message:read","conversation:read"])]
    private ?int $id = null;

    #[ORM\ManyToOne(targetEntity: User::class)]
    #[ORM\JoinColumn(name: "sender", referencedColumnName: "id", nullable: false, onDelete: "CASCADE")]
    #[Groups(["message:read","conversation:read"])]
    private ?User $sender = null;

    #[ORM\ManyToOne(targetEntity: User::class)]
    #[ORM\JoinColumn(name: "receiver", referencedColumnName: "id", nullable: false, onDelete: "CASCADE")]
    #[Groups(["message:read","conversation:read"])]
    private ?User $receiver = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: false)]
    #[Groups(["message:read","conversation:read"])]
    private ?\DateTimeInterface $date = null;

    #[ORM\ManyToOne(targetEntity: ContentMessage::class)]
    #[ORM\JoinColumn(name: "content_msg_id", referencedColumnName: "id", nullable: false)]
    #[Groups(["data_select","message:read","conversation:read"])]
    private ?ContentMessage $contentMsg = null;

    #[ORM\ManyToOne(targetEntity: State::class)]
    #[ORM\JoinColumn(name: "state_id", referencedColumnName: "id", nullable: false)]
    #[Groups(["message:read","conversation:read"])]
    private ?State $state = null;

    #[ORM\Column(length: 255)]
    #[Groups(["message:read","conversation:read"])]
    private ?string $uuid = null;

    #[ORM\Column(type: Types::DATE_MUTABLE)]
    #[Groups(["message:read","conversation:read"])]
    private ?\DateTimeInterface $created_at = null;

    #[ORM\Column(type: Types::DATE_MUTABLE)]
    #[Groups(["message:read",   "conversation:read"])]
    private ?\DateTimeInterface $updated_at = null;

    #[ORM\ManyToOne(inversedBy: 'messages')]
    private ?Conversation $conversation = null;

       public function __construct()
    {
        $this->uuid = Uuid::v7()->toString();
        $this->created_at = new \DateTime();
        $this->updated_at = new \DateTime();
    }

    // ✅ Getters & Setters

    public function getId(): ?int
    {
        return $this->id;
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

    public function getSender(): ?User
    {
        return $this->sender;
    }

    public function setSender(?User $sender): static
    {
        $this->sender = $sender;

        return $this;
    }

    public function getReceiver(): ?User
    {
        return $this->receiver;
    }

    public function setReceiver(?User $receiver): static
    {
        $this->receiver = $receiver;

        return $this;
    }

    public function getContentMsg(): ?contentMessage
    {
        return $this->contentMsg;
    }

    public function setContentMsg(?contentMessage $contentMsg): static
    {
        $this->contentMsg = $contentMsg;

        return $this;
    }

    public function getState(): ?State
    {
        return $this->state;
    }

    public function setState(?State $state): static
    {
        $this->state = $state;

        return $this;
    }

    public function getUuid(): ?String
    {
        return $this->uuid;
    }

    public function setUuid(String $uuid): static
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

    public function setUpdatedAt(\DateTimeInterface $updated_at): self
    {
        $this->updated_at = $updated_at;
        return $this;
    }

    public function getConversation(): ?Conversation
    {
        return $this->conversation;
    }

    public function setConversation(?Conversation $conversation): static
    {
        $this->conversation = $conversation;

        return $this;
    }
}
