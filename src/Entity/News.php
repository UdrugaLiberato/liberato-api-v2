<?php

namespace App\Entity;

use ApiPlatform\Doctrine\Orm\Filter\OrderFilter;
use ApiPlatform\Metadata\ApiFilter;
use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Delete;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Post;
use ApiPlatform\Metadata\Put;
use App\Repository\NewsRepository;
use DateTimeImmutable;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

#[
    ORM\Entity(repositoryClass: NewsRepository::class),
    ApiFilter(OrderFilter::class, properties: ['createdAt']),
    ApiResource(
        normalizationContext: ['groups' => ['news:read']],
        denormalizationContext: ['groups' => ['news:write']],
        paginationItemsPerPage: 100,
    ),
    GetCollection(),
    Post(
        security: "is_granted('ROLE_ADMIN')",
        securityMessage: 'Only admins can create news',
    ),
    Get(),
    Put(security: "is_granted('ROLE_ADMIN')", securityMessage: 'Only admins can edit news'),
    Delete(security: "is_granted('ROLE_ADMIN')", securityMessage: 'Only admins can delete news'),
]
class News {
  #[
      ORM\Id,
      ORM\Column(type: 'string', unique: true),
      ORM\GeneratedValue(strategy: 'CUSTOM'),
      ORM\CustomIdGenerator(class: 'doctrine.uuid_generator'),
      Groups(['news:read'])
  ]
  private ?int $id = NULL;

  #[
      ORM\Column(length: 40),
      Groups(['news:read', 'news:write'])
  ]
  private string $title;

  #[
      ORM\Column(length: 300),
      Groups(['news:read', 'news:write'])
  ]
  private string $text;

  #[
      ORM\Column(nullable: true),
      Groups(['news:read', 'news:write'])
  ]
  private ?string $linkURL = NULL;

  #[
      ORM\Column,
      Groups(['news:read'])
  ]
  private DateTimeImmutable $createdAt;

  #[ORM\Column(nullable: true)]
  private ?DateTimeImmutable $updatedAt = NULL;

  #[ORM\Column(nullable: true)]
  private ?DateTimeImmutable $deletedAt = NULL;

  #[ORM\ManyToOne(inversedBy: 'news')]
  #[ORM\JoinColumn(nullable: false)]
  private ?User $User = NULL;

  public function __construct() {
    $this->createdAt = new DateTimeImmutable();
  }

  public function getId(): ?int {
    return $this->id;
  }

  public function getTitle(): ?string {
    return $this->title;
  }

  public function setTitle(string $title): self {
    $this->title = $title;

    return $this;
  }

  public function getLinkURL(): ?string {
    return $this->linkURL;
  }

  public function setLinkURL(?string $linkURL): void {
    $this->linkURL = $linkURL;
  }

  public function getText(): ?string {
    return $this->text;
  }

  public function setText(string $text): self {
    $this->text = $text;

    return $this;
  }

  public function getCreatedAt(): ?DateTimeImmutable {
    return $this->createdAt;
  }

  public function setCreatedAt(DateTimeImmutable $createdAt): self {
    $this->createdAt = $createdAt;

    return $this;
  }

  public function getUpdatedAt(): ?DateTimeImmutable {
    return $this->updatedAt;
  }

  public function setUpdatedAt(?DateTimeImmutable $updatedAt): self {
    $this->updatedAt = $updatedAt;

    return $this;
  }

  public function getDeletedAt(): ?DateTimeImmutable {
    return $this->deletedAt;
  }

  public function setDeletedAt(?DateTimeImmutable $deletedAt): self {
    $this->deletedAt = $deletedAt;

    return $this;
  }

  public function getUser(): ?User {
    return $this->User;
  }

  public function setUser(?User $User): self {
    $this->User = $User;

    return $this;
  }
}
