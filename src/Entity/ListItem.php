<?php

namespace App\Entity;

use App\Repository\ListItemRepository;
use Doctrine\ORM\Mapping as ORM;
use Ramsey\Uuid\Doctrine\UuidGenerator;
use Ramsey\Uuid\UuidInterface;

use Symfony\Component\Serializer\Attribute\Groups;

#[ORM\Entity(repositoryClass: ListItemRepository::class)]
#[ORM\Table(name: '`ncp_list_item`')]
#[ORM\HasLifecycleCallbacks]
class ListItem
{
    #[ORM\Id]
    #[ORM\Column(type: 'uuid', unique: true)]
    #[ORM\GeneratedValue(strategy: 'CUSTOM')]
    #[ORM\CustomIdGenerator(class: UuidGenerator::class)]
    #[Groups(['list:read', 'item:read'])]
    private ?UuidInterface $id = null;

    #[ORM\ManyToOne(inversedBy: 'items')]
    #[ORM\JoinColumn(nullable: false)]
    #[Groups(['item:read'])]
    private ?ShoppingList $list = null;

    #[ORM\Column(length: 255)]
    #[Groups(['list:read', 'item:read'])]
    private ?string $name = null;

    #[ORM\Column(type: 'text', nullable: true)]
    #[Groups(['list:read', 'item:read'])]
    private ?string $description = null;

    #[ORM\Column(length: 50)]
    #[Groups(['list:read', 'item:read'])]
    private ?string $state = 'open';

    #[ORM\Column]
    #[Groups(['list:read', 'item:read'])]
    private ?float $amount = 1.0;

    #[ORM\Column]
    #[Groups(['list:read', 'item:read'])]
    private ?\DateTimeImmutable $createdAt = null;

    #[ORM\Column]
    #[Groups(['list:read', 'item:read'])]
    private ?\DateTimeImmutable $updatedAt = null;

    public function __construct()
    {
        $this->createdAt = new \DateTimeImmutable();
        $this->updatedAt = new \DateTimeImmutable();
    }

    public function getId(): ?UuidInterface
    {
        return $this->id;
    }

    public function getList(): ?ShoppingList
    {
        return $this->list;
    }

    public function setList(?ShoppingList $list): static
    {
        $this->list = $list;

        return $this;
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

    public function getAmount(): ?float
    {
        return $this->amount;
    }

    public function setAmount(float $amount): static
    {
        $this->amount = $amount;

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
