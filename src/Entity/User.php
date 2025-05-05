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
    #[Groups(["user:read", "doctor:read", "patient:read", "meeting:read","urgency:read",
    "urgentist:read", "urgency:read", "consultation:read", "message:read", "treatment:read",
    "examination:read", "notification:read", "hospitaladmin:read","urgentist:read",
    "affiliation:read", "agenda:read", "availability:read","agenthospital:read", 
    "dossier_medicale:read", "autorisation:read", "hospitaladmin:read","conversation:read",])]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Groups(["user:read", "doctor:read", "patient:read", "meeting:read","urgency:read",
    "urgentist:read", "urgency:read", "consultation:read", "message:read", "treatment:read",
    "examination:read", "notification:read", "hospitaladmin:read", "affiliation:read",
    "availability:read", "dossier_medicale:read","agenthospital:read","urgentist:read",
    "patient:read:restricted", "autorisation:read", "hospitaladmin:read"])]
    private ?string $uuid = null;

    #[ORM\Column(length: 180)]
    #[Groups(["user:read", "doctor:read", "patient:read","urgency:read",
    "urgentist:read", "urgency:read", "consultation:read", "message:read", "treatment:read",
    "examination:read", "notification:read", "hospitaladmin:read", "affiliation:read",
    "availability:read", "dossier_medicale:read","agenthospital:read", "urgentist:read",
    "patient:read:restricted", "autorisation:read", "hospitaladmin:read"])]
    private ?string $email = null;

    /**
     * @var list<string> The user roles
     */
    #[ORM\Column]
    #[Groups(["user:read", "doctor:read", "patient:read",
    "urgentist:read", "urgency:read", "consultation:read", "message:read", "treatment:read",
    "examination:read", "notification:read", "hospitaladmin:read", "affiliation:read",
    "availability:read", "dossier_medicale:read","agenthospital:read", "urgentist:read",
    "patient:read:restricted", "autorisation:read", "hospitaladmin:read"])]
    private array $roles = [];

    /**
     * @var string The hashed password
     */
    #[ORM\Column]
    // #[Groups(["user:read"])]
    private ?string $password = null;

    
    #[ORM\Column(type: "string", nullable: true)]
    #[Groups(["user:read", "doctor:read", "patient:read", "meeting:read","urgency:read",
    "urgentist:read", "urgency:read", "consultation:read", "message:read", "treatment:read",
    "examination:read", "notification:read", "hospitaladmin:read", "affiliation:read", "agenda:read",
    "availability:read", "dossier_medicale:read","agenthospital:read", "urgentist:read",
    "patient:read:restricted", "hospital:read", "autorisation:read", "hospitaladmin:read",])]
    private $first_name;

    #[ORM\Column(type: "string", nullable: true)]
    #[Groups(["data_select","user:read", "doctor:read", "patient:read","urgency:read",
    "urgentist:read", "urgency:read", "consultation:read", "message:read", "treatment:read",
    "examination:read", "notification:read", "hospitaladmin:read", "affiliation:read", "agenda:read",
    "availability:read", "dossier_medicale:read","agenthospital:read", "urgentist:read",
    "hospital:read", "autorisation:read", "hospitaladmin:read","agenthospital:read"])]
    private $last_name;

    #[ORM\Column(type: "string", nullable: true)]
    #[Groups(["user:read", "doctor:read", "patient:read", "meeting:read","urgentist:read","urgency:read",
    "urgentist:read", "urgency:read", "consultation:read", "message:read", "treatment:read",
    "examination:read", "notification:read", "hospitaladmin:read", "affiliation:read", "agenda:read",
    "availability:read", "hospital:read", "autorisation:read", "hospitaladmin:read","agenthospital:read"])]
    private $nickname;

    #[ORM\Column(type: "string", length: 255, nullable: true)]
    #[Groups(["user:read", "doctor:read", "patient:read","urgency:read",
    "urgentist:read", "urgency:read", "consultation:read", "message:read", "treatment:read",
    "examination:read", "notification:read", "hospitaladmin:read", "affiliation:read",
    "availability:read","patient:read:restricted", "urgentist:read", 
    "hospital:read", "autorisation:read", "hospitaladmin:read","agenthospital:read"])]
    private ?string $address = null;

    #[ORM\Column(type: "string", length: 255, nullable: true)]
    #[Groups(["user:read", "doctor:read", "patient:read","urgency:read",
    "urgentist:read", "urgency:read", "consultation:read", "message:read", "treatment:read",
    "examination:read", "notification:read", "hospitaladmin:read", "affiliation:read",
    "availability:read", "hospital:read", "dossier_medicale:read", "urgentist:read",
    "patient:read:restricted", "autorisation:read", "hospitaladmin:read","agenthospital:read"])]
    private ?string $tel = null;

    #[ORM\Column(type: "datetime")]
    #[Groups(["user:read", "doctor:read", "patient:read",
    "urgentist:read", "urgency:read", "consultation:read", "message:read", "treatment:read",
    "hospitaladmin:read", "affiliation:read", "hospitaladmin:read", "urgentist:read", 
    "availability:read", "hospital:read","patient:read:restricted", "autorisation:read","agenthospital:read"])]
    private $created_at;

    #[ORM\Column(type: "date", nullable: true)]
    #[Groups(["user:read", "doctor:read", "patient:read", "urgentist:read",
    "urgentist:read", "urgency:read", "consultation:read", "message:read", "treatment:read",
    "hospitaladmin:read", "affiliation:read", "availability:read", "hospital:read", 
    "dossier_medicale:read","patient:read:restricted", "autorisation:read", "hospitaladmin:read","agenthospital:read"])]
    private ?\DateTimeInterface $birth = null;

    #[ORM\Column(type: "datetime")]
    #[Groups(["user:read", "doctor:read", "patient:read","urgentist:read",
    "urgentist:read", "urgency:read", "consultation:read", "message:read", "treatment:read",
    "hospitaladmin:read", "affiliation:read", "availability:read", "hospital:read",
    "dossier_medicale:read","patient:read:restricted", "autorisation:read", "hospitaladmin:read","agenthospital:read"])]
    private $updated_at;

    /**
     * @var Collection<int, Notification>
     */
    #[ORM\OneToMany(targetEntity: Notification::class, mappedBy: 'sender_id')]
    private Collection $notifications;

    /**
     * @var Collection<int, Urgency>
     */
    #[ORM\OneToMany(targetEntity: Urgency::class, mappedBy: 'user')]
    private Collection $urgencies;

    /**
     * @var Collection<int, Autorisation>
     */
    #[ORM\OneToMany(targetEntity: Autorisation::class, mappedBy: 'demander_id')]
    private Collection $autorisations;

    #[ORM\Column(length: 255, nullable: true)]
    #[Groups(["user:read", "doctor:read", "patient:read", "meeting:read","urgency:read",
    "urgentist:read", "urgency:read", "consultation:read", "message:read", "treatment:read",
    "hospitaladmin:read", "affiliation:read","hospitaladmin:read","urgentist:read", 
    "availability:read", "hospital:read","autorisation:read", "agenthospital:read"])]
    private ?string $image = null;

    #[ORM\Column(length: 1)]
    #[Groups(["user:read", "doctor:read", "patient:read", "meeting:read","urgency:read",
    "urgentist:read", "urgency:read", "consultation:read", "message:read", "treatment:read",
    "hospitaladmin:read", "affiliation:read","hospitaladmin:read","urgentist:read", 
    "availability:read", "hospital:read","autorisation:read", "agenthospital:read"])]
    private ?string $gender = null;

    /**
     * @var Collection<int, Urgentist>
     */
    #[ORM\OneToMany(targetEntity: Urgentist::class, mappedBy: 'user')]
    private Collection $urgentists;

    /**
     * @var Collection<int, Conversations>
     */
    #[ORM\OneToMany(targetEntity: Conversation::class, mappedBy: 'receiver')]
    private Collection $conversations;

    /**
     * @var Collection<int, Conversations>
     */
    #[ORM\OneToMany(targetEntity: Conversation::class, mappedBy: 'sender')]
    private Collection $conversationsSender;


    public function __construct()
    {
        $this->uuid = Uuid::v7()->toString();
        $this->created_at = new \DateTime();
        $this->updated_at = new \DateTime();
        $this->notifications = new ArrayCollection();
        $this->urgencies = new ArrayCollection();
        $this->autorisations = new ArrayCollection();
        $this->urgentists = new ArrayCollection();
        $this->conversations = new ArrayCollection();
        $this->conversationsSender = new ArrayCollection();
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

        return $this->roles;
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
     * @return Collection<int, Autorisation>
     */
    public function getAutorisations(): Collection
    {
        return $this->autorisations;
    }

    public function addAutorisation(Autorisation $autorisation): static
    {
        if (!$this->autorisations->contains($autorisation)) {
            $this->autorisations->add($autorisation);
            $autorisation->setDemanderId($this);
        }

        return $this;
    }

    public function removeAutorisation(Autorisation $autorisation): static
    {
        if ($this->autorisations->removeElement($autorisation)) {
            // set the owning side to null (unless already changed)
            if ($autorisation->getDemanderId() === $this) {
                $autorisation->setDemanderId(null);
            }
        }

        return $this;
    }

    public function getGender(): ?string
    {
        return $this->gender;
    }

    public function setGender(string $gender): static
    {
        $this->gender = $gender;

        return $this;
    }

    public function getImage(): ?string
    {
        return $this->image;
    }

    public function setImage(?string $image): static
    {
        $this->image = $image;

        return $this;
    }

    /**
     * @return Collection<int, Urgentist>
     */
    public function getUrgentists(): Collection
    {
        return $this->urgentists;
    }

    public function addUrgentist(Urgentist $urgentist): static
    {
        if (!$this->urgentists->contains($urgentist)) {
            $this->urgentists->add($urgentist);
            $urgentist->setUser($this);
        }

        return $this;
    }

    public function removeUrgentist(Urgentist $urgentist): static
    {
        if ($this->urgentists->removeElement($urgentist)) {
            // set the owning side to null (unless already changed)
            if ($urgentist->getUser() === $this) {
                $urgentist->setUser(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Conversations>
     */
    public function getConversations(): Collection
    {
        return $this->conversations;
    }

}
