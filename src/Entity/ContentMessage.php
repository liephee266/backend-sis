<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Attribute\Groups;


#[ORM\Entity]
#[ORM\Table(name: "content_Message")]
class ContentMessage
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: "integer")]
    #[Groups(["contentmessage:read", "message:read"])]
    private ?int $id = null;

    #[ORM\Column(type: "text")]
    #[Groups(["contentmessage:read", "message:read"])]
    private ?string $msg = null;

    #[ORM\Column(type: "string", length: 255)]
    #[Groups(["contentmessage:read", "message:read"])]
    private ?string $type = null;

    #[ORM\Column(type: "string", length: 255, nullable: true)]
    #[Groups(["contentmessage:read", "message:read"])]
    private ?string $path = null;

    /**
     * @var Collection<int, Message>
     */
    #[ORM\OneToMany(targetEntity: Message::class, mappedBy: 'contentMsg')]
    private Collection $messages;

    #[ORM\Column(type: Types::DATE_MUTABLE)]
    #[Groups(["contentmessage:read", "message:read"])]
    private ?\DateTimeInterface $created_at;

    #[ORM\Column(type: Types::DATE_MUTABLE)]
    #[Groups(["contentmessage:read", "message:read"])]
    private ?\DateTimeInterface $updated_at;

    public function __construct()
    {
        $this->messages = new ArrayCollection();
        $this->created_at = new \DateTime();
        $this->updated_at = new \DateTime();
    }

    // ✅ Getters & Setters

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getMsg(): ?string
    {
        return $this->msg;
    }

    public function setMsg(string $msg): self
    {
        $this->msg = $msg;
        return $this;
    }

    public function getType(): ?string
    {
        return $this->type;
    }

    public function setType(string $type): self
    {
        $this->type = $type;
        return $this;
    }

    public function getPath(): ?string
    {
        return $this->path;
    }

    public function setPath(?string $path): self
    {
        $this->path = $path;
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
            $message->setContentMsg($this);
        }

        return $this;
    }

    public function removeMessage(Message $message): static
    {
        if ($this->messages->removeElement($message)) {
            // set the owning side to null (unless already changed)
            if ($message->getContentMsg() === $this) {
                $message->setContentMsg(null);
            }
        }

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeInterface
    {
        return $this->created_at;
    }

    public function setCreatedAt(\DateTimeInterface $created_at): static
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
