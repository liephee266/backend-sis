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
  #[Groups('notification:read')]
  private ?int $id = null;

  #[ORM\Column(type: "integer")]
  #[Groups('notification:read')]
  private ?int $sender = null;

  #[ORM\Column(type: "integer")]
  #[Groups('notification:read')]
  private ?int $receiver = null;

  #[ORM\Column(type: "text")]
  #[Groups('notification:read')]
  private ?string $content = null;

  #[ORM\Column(type: "datetime")]
  #[Groups('notification:read')]
  private ?\DateTimeInterface $dateExp = null;

  #[ORM\Column(type: "integer")]
  #[Groups('notification:read')]
  private ?int $stateId = null;

  #[ORM\Column(type: "integer")]
  #[Groups('notification:read')]
  private ?int $notifTypeId = null;

  // ✅ Getters & Setters

  public function getId(): ?int
  {
    return $this->id;
  }

  public function getSender(): ?int
  {
    return $this->sender;
  }

  public function setSender(int $sender): self
  {
    $this->sender = $sender;
    return $this;
  }

  public function getReceiver(): ?int
  {
    return $this->receiver;
  }

  public function setReceiver(int $receiver): self
  {
    $this->receiver = $receiver;
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
