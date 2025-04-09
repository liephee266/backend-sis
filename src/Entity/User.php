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
    #[Groups(["user:read", "doctor:read", "patient:read", "receptionist:read", "meeting:read",
    "urgentist:read", "urgency:read", "consultation:read", "message:read", "treatment:read",
    "examination:read", "notification:read", "hospitaladmin:read", "agenthopital:read",
    "affiliation:read", "agenda:read", "availability:read"])]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Groups(["user:read", "doctor:read", "patient:read", "receptionist:read", "meeting:read",
    "urgentist:read", "urgency:read", "consultation:read", "message:read", "treatment:read",
    "examination:read", "notification:read", "hospitaladmin:read", "agenthopital:read", "affiliation:read", "availability:read"])]
    private ?string $uuid = null;

    #[ORM\Column(length: 180)]
    #[Groups(["user:read", "doctor:read", "patient:read", "receptionist:read", "meeting:read",
    "urgentist:read", "urgency:read", "consultation:read", "message:read", "treatment:read",
    "examination:read", "notification:read", "hospitaladmin:read", "agenthopital:read", "affiliation:read", "availability:read"])]
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
    #[Groups(["user:read", "doctor:read", "patient:read", "receptionist:read", "meeting:read",
    "urgentist:read", "urgency:read", "consultation:read", "message:read", "treatment:read",
    "examination:read", "notification:read", "hospitaladmin:read", "agenthopital:read", "affiliation:read", "agenda:read", "availability:read"])]
    private $first_name;

    #[ORM\Column(type: "string", nullable: true)]
    #[Groups(["user:read", "doctor:read", "patient:read", "receptionist:read", "meeting:read",
    "urgentist:read", "urgency:read", "consultation:read", "message:read", "treatment:read",
    "examination:read", "notification:read", "hospitaladmin:read", "agenthopital:read", "affiliation:read", "agenda:read", "availability:read"])]
    private $last_name;

    #[ORM\Column(type: "string", nullable: true)]
    #[Groups(["user:read", "doctor:read", "patient:read", "receptionist:read", "meeting:read",
    "urgentist:read", "urgency:read", "consultation:read", "message:read", "treatment:read",
    "examination:read", "notification:read", "hospitaladmin:read", "agenthopital:read", "affiliation:read", "agenda:read", "availability:read"])]
    private $nickname;

    #[ORM\Column(type: "string", length: 255, nullable: true)]
    #[Groups(["user:read", "doctor:read", "patient:read", "receptionist:read", "meeting:read",
    "urgentist:read", "urgency:read", "consultation:read", "message:read", "treatment:read",
    "examination:read", "notification:read", "hospitaladmin:read", "agenthopital:read", "affiliation:read", "availability:read"])]
    private ?string $address = null;

    #[ORM\Column(type: "string", length: 255, nullable: true)]
    #[Groups(["user:read", "doctor:read", "patient:read", "receptionist:read", "meeting:read",
    "urgentist:read", "urgency:read", "consultation:read", "message:read", "treatment:read",
    "examination:read", "notification:read", "hospitaladmin:read", "agenthopital:read", "affiliation:read", "availability:read"])]
    private ?string $tel = null;

    #[ORM\Column(type: "boolean")]
    #[Groups(["user:read", "doctor:read", "patient:read", "receptionist:read", "meeting:read",
    "urgentist:read", "urgency:read", "consultation:read", "message:read", "treatment:read",
    "examination:read", "notification:read", "hospitaladmin:read", "agenthopital:read", "affiliation:read", "availability:read"])]
    private bool $gender;

    #[ORM\Column(type: "datetime")]
    #[Groups(["user:read", "doctor:read", "patient:read", "receptionist:read", "meeting:read",
    "urgentist:read", "urgency:read", "consultation:read", "message:read", "treatment:read",
    "hospitaladmin:read", "agenthopital:read", "affiliation:read", "availability:read"])]
    private $created_at;

    #[ORM\Column(type: "date", nullable: true)]
    #[Groups(["user:read", "doctor:read", "patient:read", "receptionist:read", "meeting:read",
    "urgentist:read", "urgency:read", "consultation:read", "message:read", "treatment:read",
    "hospitaladmin:read", "agenthopital:read", "affiliation:read", "availability:read"])]
    private ?\DateTimeInterface $birth = null;

    #[ORM\Column(type: "datetime")]
    #[Groups(["user:read", "doctor:read", "patient:read", "receptionist:read", "meeting:read",
    "urgentist:read", "urgency:read", "consultation:read", "message:read", "treatment:read",
    "hospitaladmin:read", "agenthopital:read", "affiliation:read", "availability:read"])]
    private $updated_at;

    /**
     * @var Collection<int, Receptionist>
     */
    #[ORM\OneToMany(targetEntity: Receptionist::class, mappedBy: 'user_id')]
    private Collection $receptionist_id;

    /**
     * @var Collection<int, Notification>
     */
    #[ORM\OneToMany(targetEntity: Notification::class, mappedBy: 'sender_id')]
    private Collection $notifications;

    /**
     * @var Collection<int, SisAdmin>
     */
    #[ORM\OneToMany(targetEntity: SisAdmin::class, mappedBy: 'user_id')]
    private Collection $sisAdmins;

    /**
     * @var Collection<int, Receptionist>
     */
    #[ORM\OneToMany(targetEntity: Receptionist::class, mappedBy: 'user')]
    private Collection $receptionists;

    public function __construct()
    {
        $this->uuid = Uuid::v7()->toString();
        $this->created_at = new \DateTime();
        $this->updated_at = new \DateTime();
        $this->receptionist_id = new ArrayCollection();
        $this->notifications = new ArrayCollection();
        $this->sisAdmins = new ArrayCollection();
        $this->receptionists = new ArrayCollection();
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
            $notification->setSenderId($this);
        }

        return $this;
    }

    public function removeNotification(Notification $notification): static
    {
        if ($this->notifications->removeElement($notification)) {
            // set the owning side to null (unless already changed)
            if ($notification->getSenderId() === $this) {
                $notification->setSenderId(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, SisAdmin>
     */
    public function getSisAdmins(): Collection
    {
        return $this->sisAdmins;
    }

    public function addSisAdmin(SisAdmin $sisAdmin): static
    {
        if (!$this->sisAdmins->contains($sisAdmin)) {
            $this->sisAdmins->add($sisAdmin);
            $sisAdmin->setUserId($this);
        }

        return $this;
    }

    public function removeSisAdmin(SisAdmin $sisAdmin): static
    {
        if ($this->sisAdmins->removeElement($sisAdmin)) {
            // set the owning side to null (unless already changed)
            if ($sisAdmin->getUserId() === $this) {
                $sisAdmin->setUserId(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Receptionist>
     */
    public function getReceptionists(): Collection
    {
        return $this->receptionists;
    }

    public function addReceptionist(Receptionist $receptionist): static
    {
        if (!$this->receptionists->contains($receptionist)) {
            $this->receptionists->add($receptionist);
            $receptionist->setUser($this);
        }

        return $this;
    }

    public function removeReceptionist(Receptionist $receptionist): static
    {
        if ($this->receptionists->removeElement($receptionist)) {
            // set the owning side to null (unless already changed)
            if ($receptionist->getUser() === $this) {
                $receptionist->setUser(null);
            }
        }

        return $this;
    }

}
