<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: "notification")]
class Notification
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: "integer")]
    private ?int $id = null;

    #[ORM\Column(type: "integer")]
    private ?int $from = null;

    #[ORM\Column(type: "integer")]
    private ?int $to = null;

    #[ORM\Column(type: "text")]
    private ?string $content = null;

    #[ORM\Column(type: "datetime")]
    private ?\DateTimeInterface $dateExp = null;

    #[ORM\Column(type: "integer")]
    private ?int $stateId = null;

    #[ORM\Column(type: "integer")]
    private ?int $notifTypeId = null;

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

    public function getStateId(): ?int
    {
        return $this->stateId;
    }

    public function setStateId(int $stateId): self
    {
        $this->stateId = $stateId;
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
}
