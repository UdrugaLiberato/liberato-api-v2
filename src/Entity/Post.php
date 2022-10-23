<?php

declare(strict_types=1);

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Delete;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Put;
use App\Controller\UpdatePostController;
use App\DTO\Post\PostInput;
use App\Repository\PostRepository;
use App\State\PostProcessor;
use DateTimeImmutable;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

#[ApiResource(normalizationContext: ['groups' => ['post:read']],),
    GetCollection(),
    \ApiPlatform\Metadata\Post(
        inputFormats: ['multipart' => ['multipart/form-data']],
        security: "is_granted('ROLE_ADMIN')",
        securityMessage: 'Only admins can create posts',
        input: PostInput::class,
        processor: PostProcessor::class
    ),
    Get(),
    Put(
        inputFormats: ['multipart' => ['multipart/form-data']],
        controller: UpdatePostController::class,
        security: "is_granted('ROLE_ADMIN')",
        securityMessage: 'Only admins can edit posts',
        deserialize: false
    ),
    Delete(
        security: "is_granted('ROLE_ADMIN')",
        securityMessage: 'Only admins can delete posts',
    ),
    ORM\Entity(repositoryClass: PostRepository::class)]
class Post
{
    #[
        ORM\Id,
        ORM\Column(type: 'string', unique: true),
        ORM\GeneratedValue(strategy: 'CUSTOM'),
        ORM\CustomIdGenerator(class: 'doctrine.uuid_generator'),
        Groups(['post:read'])
    ]
    private string $id;

    #[
        ORM\ManyToOne(targetEntity: User::class, cascade: ['remove'], inversedBy: 'posts'),
        ORM\JoinColumn(name: 'user_id', referencedColumnName: 'id', nullable: false, onDelete: 'CASCADE'),
        Assert\NotNull,
        Groups(['post:read'])
    ]
    private User $author;

    #[
        ORM\Column(type: 'string', length: 255, unique: true),
        Assert\Length(min: 10, minMessage: 'Title must be at least {{ limit }} characters long!'),
        Groups(['post:read'])
    ]
    private string $title;

    #[
        ORM\Column(type: 'text'),
        Assert\Length(min: 125, minMessage: 'Body should be at least {{ limit }} characters long!'),
        Groups(['post:read'])
    ]
    private string $body;

    /**
     * @var string[]
     */
    #[ORM\Column(type: 'array'), Groups(['post:read'])]
    private array $tags;

    #[ORM\Column(type: 'array'), Groups(['post:read'])]
    private ArrayCollection $images;

    #[
        ORM\Column(type: 'datetime_immutable'),
        Groups(['post:read'])
    ]
    private DateTimeImmutable $createdAt;

    #[ORM\Column(type: 'datetime_immutable', nullable: true), Groups(['post:read'])]
    private ?DateTimeImmutable $updatedAt;

    #[ORM\Column(type: 'datetime_immutable', nullable: true), Groups(['post:read'])]
    private ?DateTimeImmutable $deletedAt;

    public function __construct()
    {
        $this->images = new ArrayCollection();
        $this->createdAt = new DateTimeImmutable('now');
        $this->deletedAt = null;
        $this->updatedAt = null;
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function getAuthor(): User
    {
        return $this->author;
    }

    public function setAuthor(?User $author): self
    {
        $this->author = $author;

        return $this;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(string $title): self
    {
        $this->title = $title;

        return $this;
    }

    public function getBody(): ?string
    {
        return $this->body;
    }

    public function setBody(string $body): self
    {
        $this->body = $body;

        return $this;
    }

    public function getCreatedAt(): ?DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function getUpdatedAt(): ?DateTimeImmutable
    {
        return $this->updatedAt;
    }

    public function setUpdatedAt(?DateTimeImmutable $updatedAt): self
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }

    public function getDeletedAt(): ?DateTimeImmutable
    {
        return $this->deletedAt;
    }

    public function setDeletedAt(?DateTimeImmutable $deletedAt): self
    {
        $this->deletedAt = $deletedAt;

        return $this;
    }

    /**
     * @return string[]
     */
    public function getTags(): array
    {
        return $this->tags;
    }

    /**
     * @param string[] $tags
     */
    public function setTags(array $tags): void
    {
        $this->tags = $tags;
    }

    public function getImages(): ArrayCollection
    {
        return $this->images;
    }

    public function setImages(ArrayCollection $images): void
    {
        $this->images = $images;
    }
}
