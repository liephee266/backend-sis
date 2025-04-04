<?php

namespace App\Entity;

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
    private ?int $id = null;

    #[ORM\Column(type: "integer")]
    #[Groups(['notification:read'])]
    private ?int $from = null;

    #[ORM\Column(type: "integer")]
    #[Groups(['notification:read'])]
    private ?int $to = null;

    #[ORM\Column(type: "text")]
    #[Groups(['notification:read'])]
    private ?string $content = null;

    #[ORM\Column(type: "datetime")]
    #[Groups(['notification:read'])]
    private ?\DateTimeInterface $dateExp = null;

    #[ORM\ManyToOne(inversedBy: 'notifications')]
    #[Groups(['notification:read', 'notification_type:read'])]
    private ?State $state = null;

    #[ORM\ManyToOne(inversedBy: 'notifications')]
    #[Groups(['notification:read', 'notification_type:read'])]
    private ?NotificationType $notificationType = null;

    // ✅ Getters & Setters

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getFrom(): ?int
    {
        return $this->from;
    }

    public function setFrom(int $from): self
    {
        $this->from = $from;
        return $this;
    }

    public function getTo(): ?int
    {
        return $this->to;
    }

    public function setTo(int $to): self
    {
        $this->to = $to;
        return $this;
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

    public function getDateExp(): ?\DateTimeInterface
    {
        return $this->dateExp;
    }

    public function setDateExp(\DateTimeInterface $dateExp): self
    {
        $this->dateExp = $dateExp;
        return $this;
    }
    public function getNotifTypeId(): ?int
    {
        return $this->notifTypeId;
    }

    public function setNotifTypeId(int $notifTypeId): self
    {
        $this->notifTypeId = $notifTypeId;
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

    public function getNotificationType(): ?NotificationType
    {
        return $this->notificationType;
    }

    public function setNotificationType(?NotificationType $notificationType): static
    {
        $this->notificationType = $notificationType;

        return $this;
    }
}
