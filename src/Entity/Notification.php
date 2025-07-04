<?php

namespace App\Entity;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Attribute\Groups;
use Symfony\Component\Uid\Uuid;


#[ORM\Entity]
#[ORM\Table(name: "notification")]
class Notification
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: "integer")]
    #[Groups(["data_select",'notification:read'])]
    private ?int $id = null;

    #[ORM\Column(type: "text")]
    #[Groups(["data_select",'notification:read'])]
    private ?string $content = null;

    #[ORM\ManyToOne(inversedBy: 'notifications')]
    #[ORM\JoinColumn(nullable: true)]
    #[Groups(['notification:read'])]
    private ?NotificationType $notification_type_id = null;

    #[ORM\Column(length: 255)]
    #[Groups(["notification:read"])]
    private ?string $uuid = null;

    #[ORM\Column(type: Types::DATE_MUTABLE)]
    #[Groups(["notification:read"])]
    private ?\DateTimeInterface $created_at = null;

    #[ORM\Column(type: Types::DATE_MUTABLE)]
    #[Groups(["notification:read"])]
    private ?\DateTimeInterface $updated_at = null;

    #[ORM\Column(length: 255)]
    #[Groups(["notification:read"])]
    private ?string $title = null;

    #[ORM\Column]
    #[Groups(["notification:read"])]
    private ?bool $isRead = false;

    #[ORM\ManyToOne(inversedBy: 'notification')]
    #[ORM\JoinColumn(nullable: true)]
    #[Groups(["notification:read"])]
    private ?User $receiver = null;

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

    public function getContent(): ?string
    {
        return $this->content;
    }

    public function setContent(string $content): self
    {
        $this->content = $content;
        return $this;
    }

    public function getNotificationTypeId(): ?NotificationType
    {
        return $this->notification_type_id;
    }

    public function setNotificationTypeId(?NotificationType $notification_type_id): static
    {
        $this->notification_type_id = $notification_type_id;

        return $this;
    }

    public function getNotificationType(): ?NotificationType
    {
        return $this->notification_type_id;
    }

    public function setNotificationType(?NotificationType $notification_type_id): static
    {
        $this->notification_type_id = $notification_type_id;

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

    public function setUpdatedAt(\DateTimeInterface $updated_at): static
    {
        $this->updated_at = $updated_at;

        return $this;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(string $title): static
    {
        $this->title = $title;

        return $this;
    }

    public function isRead(): ?bool
    {
        return $this->isRead;
    }

    public function setIsRead(bool $isRead): static
    {
        $this->isRead = $isRead;

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
}
