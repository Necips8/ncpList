<?php

namespace App\Entity;

use App\Repository\ShoppingListRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Ramsey\Uuid\Doctrine\UuidGenerator;
use Ramsey\Uuid\UuidInterface;

use Symfony\Component\Serializer\Attribute\Groups;

#[ORM\Entity(repositoryClass: ShoppingListRepository::class)]
#[ORM\Table(name: '`ncp_list`')]
#[ORM\HasLifecycleCallbacks]
class ShoppingList
{
    #[ORM\Id]
    #[ORM\Column(type: 'uuid', unique: true)]
    #[ORM\GeneratedValue(strategy: 'CUSTOM')]
    #[ORM\CustomIdGenerator(class: UuidGenerator::class)]
    #[Groups(['list:read', 'item:read'])]
    private ?UuidInterface $id = null;

    #[ORM\Column(length: 255)]
    #[Groups(['list:read', 'item:read'])]
    private ?string $name = null;

    #[ORM\Column(type: 'text', nullable: true)]
    #[Groups(['list:read', 'item:read'])]
    private ?string $description = null;

    #[ORM\Column(length: 50)]
    #[Groups(['list:read', 'item:read'])]
    private ?string $state = 'active';

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $owner = null;

    #[ORM\Column]
    #[Groups(['list:read'])]
    private ?\DateTimeImmutable $createdAt = null;

    #[ORM\Column]
    #[Groups(['list:read'])]
    private ?\DateTimeImmutable $updatedAt = null;

    /**
     * @var Collection<int, ListItem>
     */
    #[ORM\OneToMany(targetEntity: ListItem::class, mappedBy: 'list', orphanRemoval: true)]
    #[Groups(['list:read'])]
    private Collection $items;

    public function __construct()
    {
        $this->items = new ArrayCollection();
        $this->createdAt = new \DateTimeImmutable();
        $this->updatedAt = new \DateTimeImmutable();
    }

    public function getId(): ?UuidInterface
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): static
    {
        $this->name = $name;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): static
    {
        $this->description = $description;

        return $this;
    }

    public function getState(): ?string
    {
        return $this->state;
    }

    public function setState(string $state): static
    {
        $this->state = $state;

        return $this;
    }

    public function getOwner(): ?User
    {
        return $this->owner;
    }

    public function setOwner(?User $owner): static
    {
        $this->owner = $owner;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function getUpdatedAt(): ?\DateTimeImmutable
    {
        return $this->updatedAt;
    }

    /**
     * @return Collection<int, ListItem>
     */
    public function getItems(): Collection
    {
        return $this->items;
    }

    public function addItem(ListItem $item): static
    {
        if (!$this->items->contains($item)) {
            $this->items->add($item);
            $item->setList($this);
        }

        return $this;
    }

    public function removeItem(ListItem $item): static
    {
        if ($this->items->removeElement($item)) {
            // set the owning side to null (unless already changed)
            if ($item->getList() === $this) {
                $item->setList(null);
            }
        }

        return $this;
    }

    #[ORM\PrePersist]
    public function setInitialTimestamps(): void
    {
        $this->createdAt = new \DateTimeImmutable();
        $this->updatedAt = new \DateTimeImmutable();
    }

    #[ORM\PreUpdate]
    public function updateTimestamp(): void
    {
        $this->updatedAt = new \DateTimeImmutable();
    }
}
