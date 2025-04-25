<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Attribute\Groups;

#[ORM\Entity]
#[ORM\Table(name: "state")]
class State
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: "integer")]
    #[Groups(["data_select","state:read", "message:read", "notification:read"])]
    private ?int $id = null;

    #[ORM\Column(type: "string", length: 255)]
    #[Groups(["data_select","state:read", "message:read", "notification:read"])]
    private ?string $name = null;

    /**
     * @var Collection<int, Notification>
     */
    #[ORM\OneToMany(targetEntity: Notification::class, mappedBy: 'state_id')]
    private Collection $notifications;

    /**
     * @var Collection<int, Meeting>
     */
    #[ORM\OneToMany(targetEntity: Meeting::class, mappedBy: 'state_id')]
    private Collection $meetings;

    #[ORM\Column(length: 255)]
    private ?string $tech_name = null;

    public function __construct()
    {
        $this->notifications = new ArrayCollection();
        $this->meetings = new ArrayCollection();
    }

    // ✅ Getters & Setters

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;
        return $this;
    }

    /**
     * @return Collection<int, Notification>
     */
    public function getNotifications(): Collection
    {
        return $this->notifications;
    }

    public function addNotification(Notification $notification): static
    {
        if (!$this->notifications->contains($notification)) {
            $this->notifications->add($notification);
            $notification->setStateId($this);
        }

        return $this;
    }

    public function removeNotification(Notification $notification): static
    {
        if ($this->notifications->removeElement($notification)) {
            // set the owning side to null (unless already changed)
            if ($notification->getStateId() === $this) {
                $notification->setStateId(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Meeting>
     */
    public function getMeetings(): Collection
    {
        return $this->meetings;
    }

    public function addMeeting(Meeting $meeting): static
    {
        if (!$this->meetings->contains($meeting)) {
            $this->meetings->add($meeting);
            $meeting->setStateId($this);
        }

        return $this;
    }

    public function removeMeeting(Meeting $meeting): static
    {
        if ($this->meetings->removeElement($meeting)) {
            // set the owning side to null (unless already changed)
            if ($meeting->getStateId() === $this) {
                $meeting->setStateId(null);
            }
        }

        return $this;
    }

    public function getTechName(): ?string
    {
        return $this->tech_name;
    }

    public function setTechName(string $tech_name): static
    {
        $this->tech_name = $tech_name;

        return $this;
    }
}
