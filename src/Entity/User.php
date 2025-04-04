<?php

namespace App\Entity;

use App\Entity\Location;
use Doctrine\ORM\Mapping as ORM;
use App\Repository\UserRepository;
use Symfony\Component\Uid\Uuid; // Add this line
use Symfony\Component\Serializer\Attribute\Groups;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;


#[ORM\Entity(repositoryClass: UserRepository::class)]
#[ORM\UniqueConstraint(name: 'UNIQ_IDENTIFIER_EMAIL', fields: ['email'])]
#[ORM\Table(name: "users")]
class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(["user:read", "agent_hopital:read", "receptionist:read", "doctor:read", "hospital_admin:read", "hospital:read"])]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Groups(["user:read"])]
    private ?string $uuid = null;

    #[ORM\Column(length: 180)]
    #[Groups(["user:read"])]
    private ?string $email = null;

    /**
     * @var list<string> The user roles
     */
    #[ORM\Column]
    #[Groups(["user:read"])]
    private array $roles = [];

    /**
     * @var string The hashed password
     */
    #[ORM\Column]
    // #[Groups(["user:read"])]
    private ?string $password = null;

    
    #[ORM\Column(type: "string", nullable: true)]
    #[Groups(["user:read"])]
    private $first_name;

    #[ORM\Column(type: "string", nullable: true)]
    #[Groups(["user:read"])]
    private $last_name;

    #[ORM\Column(type: "string", nullable: true)]
    #[Groups(["user:read"])]
    private $nickname;

    #[ORM\Column(type: "string", length: 255, nullable: true)]
    private ?string $address = null;

    #[ORM\Column(type: "string", length: 255, nullable: true)]
    private ?string $tel = null;

    #[ORM\Column(type: "boolean")]
    private bool $gender;

    #[ORM\Column(type: "datetime")]
    #[Groups(["user:read"])]
    private $created_at;

    #[ORM\Column(type: "date", nullable: true)]
    private ?\DateTimeInterface $birth = null;

    #[ORM\Column(type: "datetime")]
    #[Groups(["user:read"])]
    private $updated_at;

    /**
     * @var Collection<int, Receptionist>
     */
    #[ORM\OneToMany(targetEntity: Receptionist::class, mappedBy: 'user_id')]
    private Collection $receptionist_id;

    /**
     * @var Collection<int, AgentHopital>
     */
    #[ORM\OneToMany(targetEntity: AgentHopital::class, mappedBy: 'user')]
    private Collection $agentHopitals;

    /**
     * @var Collection<int, Doctor>
     */
    #[ORM\OneToMany(targetEntity: Doctor::class, mappedBy: 'user')]
    private Collection $doctors;

    /**
     * @var Collection<int, HospitalAdmin>
     */
    #[ORM\OneToMany(targetEntity: HospitalAdmin::class, mappedBy: 'user')]
    private Collection $hospitalAdmins;

    /**
     * @var Collection<int, Message>
     */
    #[ORM\OneToMany(targetEntity: Message::class, mappedBy: 'sender')]
    private Collection $messages;

    /**
     * @var Collection<int, Message>
     */
    #[ORM\OneToMany(targetEntity: Message::class, mappedBy: 'receiver')]
    private Collection $message;

    public function __construct()
    {
        $this->uuid = Uuid::v7()->toString();
        $this->created_at = new \DateTime();
        $this->updated_at = new \DateTime();
        $this->receptionist_id = new ArrayCollection();
        $this->agentHopitals = new ArrayCollection();
        $this->doctors = new ArrayCollection();
        $this->hospitalAdmins = new ArrayCollection();
        $this->messages = new ArrayCollection();
        $this->message = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUuid(): ?string
    {
        return $this->uuid;
    }

    public function setUuid(string $uuid): static
    {
        $this->uuid = $uuid;
        return $this;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): static
    {
        $this->email = $email;

        return $this;
    }

    /**
     * A visual identifier that represents this user.
     *
     * @see UserInterface
     */
    public function getUserIdentifier(): string
    {
        return (string) $this->email;
    }

    /**
     * @see UserInterface
     *
     * @return list<string>
     */
    public function getRoles(): array
    {
        $roles = $this->roles;
        // guarantee every user at least has ROLE_USER
        $roles[] = 'ROLE_USER';

        return array_unique($roles);
    }

    /**
     * @param list<string> $roles
     */
    public function setRoles(array $roles): static
    {
        $this->roles = $roles;
        return $this;
    }

    /**
     * @see PasswordAuthenticatedUserInterface
     */
    public function getPassword(): ?string
    {
        return $this->password;
    }

    public function setPassword(string $password): static
    {
        $this->password = $password;
        return $this;
    }

    /**
     * @see UserInterface
     * @return string|null The salt
     * @codeCoverageIgnore
     * @deprecated since Symfony 5.3, use sodium_compat or paragonie/sodium_compat to handle password salts
     * 
     */
    public function eraseCredentials(): void
    {
        // If you store any temporary, sensitive data on the user, clear it here
        // $this->plainPassword = null;
    }

    
    public function getFirstName(): ?string
    {
        return $this->first_name;
    }

    public function setFirstName(?string $first_name): self
    {
        $this->first_name = $first_name;
        return $this;
    }

    public function getLastName(): ?string
    {
        return $this->last_name;
    }

    public function setLastName(?string $last_name): self
    {
        $this->last_name = $last_name;
        return $this;
    }

    public function getNickname(): ?string
    {
        return $this->nickname;
    }

    public function setNickname(?string $nickname): self
    {
        $this->nickname = $nickname;
        return $this;
    }
    public function getAddress(): ?string
    {
        return $this->address;
    }

    public function setAddress(?string $address): self
    {
        $this->address = $address;
        return $this;
    }

    public function getTel(): ?string
    {
        return $this->tel;
    }

    public function setTel(?string $tel): self
    {
        $this->tel = $tel;
        return $this;
    }
    public function getGender(): bool
    {
        return $this->gender;
    }

    public function setGender(bool $gender): self
    {
        $this->gender = $gender;
        return $this;

    }
    public function getBirth(): ?\DateTimeInterface
    {
        return $this->birth;
    }

    public function setBirth(?\DateTimeInterface $birth): self
    {
        $this->birth = $birth;
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

    /**
     * @return Collection<int, Receptionist>
     */
    public function getReceptionistId(): Collection
    {
        return $this->receptionist_id;
    }

    public function addReceptionistId(Receptionist $receptionistId): static
    {
        if (!$this->receptionist_id->contains($receptionistId)) {
            $this->receptionist_id->add($receptionistId);
            $receptionistId->setUserId($this);
        }

        return $this;
    }

    public function removeReceptionistId(Receptionist $receptionistId): static
    {
        if ($this->receptionist_id->removeElement($receptionistId)) {
            // set the owning side to null (unless already changed)
            if ($receptionistId->getUserId() === $this) {
                $receptionistId->setUserId(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, AgentHopital>
     */
    public function getAgentHopitals(): Collection
    {
        return $this->agentHopitals;
    }

    public function addAgentHopital(AgentHopital $agentHopital): static
    {
        if (!$this->agentHopitals->contains($agentHopital)) {
            $this->agentHopitals->add($agentHopital);
            $agentHopital->setUser($this);
        }

        return $this;
    }

    public function removeAgentHopital(AgentHopital $agentHopital): static
    {
        if ($this->agentHopitals->removeElement($agentHopital)) {
            // set the owning side to null (unless already changed)
            if ($agentHopital->getUser() === $this) {
                $agentHopital->setUser(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Doctor>
     */
    public function getDoctors(): Collection
    {
        return $this->doctors;
    }

    public function addDoctor(Doctor $doctor): static
    {
        if (!$this->doctors->contains($doctor)) {
            $this->doctors->add($doctor);
            $doctor->setUser($this);
        }

        return $this;
    }

    public function removeDoctor(Doctor $doctor): static
    {
        if ($this->doctors->removeElement($doctor)) {
            // set the owning side to null (unless already changed)
            if ($doctor->getUser() === $this) {
                $doctor->setUser(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, HospitalAdmin>
     */
    public function getHospitalAdmins(): Collection
    {
        return $this->hospitalAdmins;
    }

    public function addHospitalAdmin(HospitalAdmin $hospitalAdmin): static
    {
        if (!$this->hospitalAdmins->contains($hospitalAdmin)) {
            $this->hospitalAdmins->add($hospitalAdmin);
            $hospitalAdmin->setUser($this);
        }

        return $this;
    }

    public function removeHospitalAdmin(HospitalAdmin $hospitalAdmin): static
    {
        if ($this->hospitalAdmins->removeElement($hospitalAdmin)) {
            // set the owning side to null (unless already changed)
            if ($hospitalAdmin->getUser() === $this) {
                $hospitalAdmin->setUser(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Message>
     */
    public function getMessages(): Collection
    {
        return $this->messages;
    }

    public function addMessage(Message $message): static
    {
        if (!$this->messages->contains($message)) {
            $this->messages->add($message);
            $message->setSender($this);
        }

        return $this;
    }

    public function removeMessage(Message $message): static
    {
        if ($this->messages->removeElement($message)) {
            // set the owning side to null (unless already changed)
            if ($message->getSender() === $this) {
                $message->setSender(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Message>
     */
    public function getMessage(): Collection
    {
        return $this->message;
    }

}
