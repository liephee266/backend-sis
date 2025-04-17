<?php

namespace App\Entity;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Attribute\Groups;


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
    #[ORM\JoinColumn(nullable: false)]
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

    // ✅ Getters & Setters

    public function __construct()
    {
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
}
