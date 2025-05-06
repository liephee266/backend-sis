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
    #[Groups(['notification:read'])]
    #[Groups(["data_select",'notification:read'])]
    private ?int $id = null;

    #[ORM\Column(type: "text")]
    #[Groups(['notification:read'])]
    #[Groups(["data_select",'notification:read'])]
    private ?string $content = null;

    #[ORM\ManyToOne(inversedBy: 'notifications')]
    #[ORM\JoinColumn(nullable: true)]
    #[Groups(['notification:read'])]
    private ?NotificationType $notification_type_id = null;

    #[ORM\ManyToOne(inversedBy: 'notifications')]
    #[ORM\JoinColumn(nullable: false)]
    #[Groups(['notification:read'])]
    private ?State $state_id = null;

    #[ORM\ManyToOne(inversedBy: 'notifications')]
    #[ORM\JoinColumn(nullable: false)]
    #[Groups(['notification:read'])]
    private ?User $sender_id = null;

    #[ORM\ManyToOne(inversedBy: 'notifications')]
    #[ORM\JoinColumn(nullable: false)]
    #[Groups(['notification:read'])]
    private ?User $receiver_id = null;

    #[ORM\Column(type: "datetime")]
    #[Groups(['notification:read'])]
    private $date_exp;

    #[ORM\Column(length: 255)]
    #[Groups(["notification:read"])]
    private ?string $uuid = null;

    #[ORM\Column(type: Types::DATE_MUTABLE)]
    private ?\DateTimeInterface $created_at = null;

    #[ORM\Column(type: Types::DATE_MUTABLE)]
    private ?\DateTimeInterface $updated_at = null;

       public function __construct()
    {
        $this->uuid = Uuid::v7()->toString();
        $this->created_at = new \DateTime();
        $this->updated_at = new \DateTime();
        $this->date_exp = new \DateTime();
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

    public function getStateId(): ?State
    {
        return $this->state_id;
    }

    public function setStateId(?State $state_id): static
    {
        $this->state_id = $state_id;

        return $this;
    }

    public function getSenderId(): ?User
    {
        return $this->sender_id;
    }

    public function setSenderId(?User $sender_id): static
    {
        $this->sender_id = $sender_id;

        return $this;
    }

    public function getReceiverId(): ?User
    {
        return $this->receiver_id;
    }

    public function setReceiverId(?User $receiver_id): static
    {
        $this->receiver_id = $receiver_id;

        return $this;
    }

    public function getDateExp(): ?\DateTimeInterface
    {
        return $this->date_exp;
    }

    public function setDateExp(\DateTimeInterface $date_exp): self
    {
        $this->date_exp = $date_exp;
        return $this;
    }

    public function getState(): ?State
    {
        return $this->state_id;
    }

    public function setState(?State $state_id): static
    {
        $this->state_id = $state_id;

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
}
