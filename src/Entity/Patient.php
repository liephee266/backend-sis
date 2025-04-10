<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use App\Entity\User;
use Symfony\Component\Serializer\Attribute\Groups;

#[ORM\Entity]
#[ORM\Table(name: "patient")]
class Patient
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: "integer", unique: true)]
    #[Groups(["patient:read", "meeting:read", "urgency:read", "consultation:read", "treatment:read", "examination:read"])]
    private ?int $id = null;

    #[ORM\ManyToOne(targetEntity: User::class)]
    #[ORM\JoinColumn(name: "user_id", referencedColumnName: "id", nullable: false, onDelete: "CASCADE")]
    #[Groups(["patient:read", "meeting:read", "urgency:read", "consultation:read", "treatment:read", "examination:read"])]
    private ?User $user = null;

    #[ORM\ManyToOne(targetEntity: User::class)]
    #[ORM\JoinColumn(name: "tutor_id", referencedColumnName: "id", nullable: true, onDelete: "SET NULL")]
    #[Groups(["patient:read", "meeting:read", "urgency:read", "consultation:read", "treatment:read", "examination:read"])]
    private ?User $tutor = null;

    /**
     * @var Collection<int, Meeting>
     */
    #[ORM\OneToMany(targetEntity: Meeting::class, mappedBy: 'patient_id')]
    private Collection $meeting_id;

    /**
     * @var Collection<int, Consultation>
     */
    #[ORM\OneToMany(targetEntity: Consultation::class, mappedBy: 'patient')]
    private Collection $consultations;

    public function __construct()
    {
        $this->meeting_id = new ArrayCollection();
        $this->consultations = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }


    /**
     * @return Collection<int, Consultation>
     */
    public function getConsultations(): Collection
    {
        return $this->consultations;
    }

    public function addConsultation(Consultation $consultation): static
    {
        if (!$this->consultations->contains($consultation)) {
            $this->consultations->add($consultation);
            $consultation->setPatient($this);
        }

        return $this;
    }

    public function removeConsultation(Consultation $consultation): static
    {
        if ($this->consultations->removeElement($consultation)) {
            // set the owning side to null (unless already changed)
            if ($consultation->getPatient() === $this) {
                $consultation->setPatient(null);
            }
        }

        return $this;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): static
    {
        $this->user = $user;

        return $this;
    }

    public function getTutor(): ?User
    {
        return $this->tutor;
    }

    public function setTutor(?User $tutor): static
    {
        $this->tutor = $tutor;

        return $this;
    }

    /**
     * @return Collection<int, Meeting>
     */
    public function getMeetingId(): Collection
    {
        return $this->meeting_id;
    }

    public function addMeetingId(Meeting $meetingId): static
    {
        if (!$this->meeting_id->contains($meetingId)) {
            $this->meeting_id->add($meetingId);
            $meetingId->setPatientId($this);
        }

        return $this;
    }

    public function removeMeetingId(Meeting $meetingId): static
    {
        if ($this->meeting_id->removeElement($meetingId)) {
            // set the owning side to null (unless already changed)
            if ($meetingId->getPatientId() === $this) {
                $meetingId->setPatientId(null);
            }
        }

        return $this;
    }
}
