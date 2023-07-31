<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiProperty;
use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Post;
use App\DTO\Task\TaskInput;
use App\Repository\TaskRepository;
use App\State\TaskPostProcessor;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

#[
    ORM\Entity(repositoryClass: TaskRepository::class),
    ApiResource(
        normalizationContext: ['groups' => ['task:read']],
    ),
    GetCollection(
        paginationEnabled: true,
        paginationItemsPerPage: 30,
        security: "is_granted('ROLE_ADMIN')",
        securityMessage: "Only admins can see all tasks."
    ),
  Get(
      security: "is_granted('ROLE_ADMIN') or object.getAssignedTo() == user",
      securityMessage: "You can only see tasks assigned to you."
  ),
  Post(
      security: "is_granted('ROLE_ADMIN') or object.getAssignedTo() == user",
      securityMessage: "You can only create tasks assigned to you.",
      input: TaskInput::class,
      processor: TaskPostProcessor::class
  )
]
class Task {
  #[
      ApiProperty(identifier: true),
      ORM\Id,
      ORM\Column(type: 'string', unique: true),
      ORM\GeneratedValue(strategy: 'CUSTOM'),
      ORM\CustomIdGenerator(class: 'doctrine.uuid_generator'),
      Groups(['task:read'])
  ]
  private $id;

  #[
      ORM\Column(length: 255),
      Groups(['task:read'])
  ]
  private ?string $name = NULL;

  #[
       ORM\Column(length: 255),
      Groups(['task:read'])
  ]
  private ?string $priority = NULL;

  #[
      ORM\Column,
      Groups(['task:read'])
  ]
  private ?bool $isFinished = NULL;

  #[
      ORM\ManyToOne(inversedBy: 'tasks'),
      Groups(['task:read'])
  ]
  private ?User $assignedTo = NULL;
  #[
      ORM\Column(nullable: true),
      Groups(['task:read'])
  ]
  private ?\DateTimeImmutable $finishedAt = NULL;

  #[
      ORM\Column(type: Types::TEXT, nullable: true),
      Groups(['task:read'])
  ]
  private ?string $note = NULL;

  #[
      ORM\Column,
      Groups(['task:read'])
  ]
  private ?\DateTimeImmutable $deadline = NULL;

  #[
      ORM\Column,
      Groups(['task:read'])
  ]
  private \DateTimeImmutable $createdAt;

  #[
      ORM\Column(nullable: true),
      Groups(['task:read'])
  ]
  private ?\DateTimeImmutable $updatedAt = NULL;

  public function __construct() {
    $this->createdAt = new \DateTimeImmutable();
    $this->isFinished = false;
  }

  public function getId(): string {
    return $this->id;
  }

  public function getName(): ?string {
    return $this->name;
  }

  public function setName(string $name): static {
    $this->name = $name;

    return $this;
  }

  public function getPriority(): ?string {
    return $this->priority;
  }

  public function setPriority(string $priority): static {
    $this->priority = $priority;

    return $this;
  }

  public function isIsFinished(): ?bool {
    return $this->isFinished;
  }

  public function setIsFinished(bool $isFinished): static {
    $this->isFinished = $isFinished;

    return $this;
  }

  public function getAssignedTo(): ?User {
    return $this->assignedTo;
  }

  public function setAssignedTo(?User $assignedTo): static {
    $this->assignedTo = $assignedTo;

    return $this;
  }

  public function getDeadline(): ?\DateTimeImmutable {
    return $this->deadline;
  }

  public function setDeadline(\DateTimeImmutable $deadline): static {
    $this->deadline = $deadline;

    return $this;
  }

  public function getCreatedAt(): ?\DateTimeImmutable {
    return $this->createdAt;
  }

  public function setCreatedAt(\DateTimeImmutable $createdAt): static {
    $this->createdAt = $createdAt;

    return $this;
  }

  public function getUpdatedAt(): ?\DateTimeImmutable {
    return $this->updatedAt;
  }

  public function setUpdatedAt(?\DateTimeImmutable $updatedAt): static {
    $this->updatedAt = $updatedAt;

    return $this;
  }

  public function getFinishedAt(): ?\DateTimeImmutable {
    return $this->finishedAt;
  }

  public function setFinishedAt(?\DateTimeImmutable $finishedAt): static {
    $this->finishedAt = $finishedAt;

    return $this;
  }

  public function getNote(): ?string {
    return $this->note;
  }

  public function setNote(?string $note): static {
    $this->note = $note;

    return $this;
  }
}
