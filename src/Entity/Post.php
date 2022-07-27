<?php

declare(strict_types=1);

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiResource;
use App\Controller\UpdatePostController;
use App\DTO\Post\PostInput;
use App\DTO\Post\PostOutput;
use App\Repository\PostRepository;
use DateTimeImmutable;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ApiResource(collectionOperations: [
    'get',
    'post' => [
        'input' => PostInput::class,
        'security' => "is_granted('ROLE_ADMIN')",
        'security_message' => 'Only admins can add posts.',
        'input_formats' => [
            'multipart' => ['multipart/form-data'],
        ],
    ],
], itemOperations: [
    'get',
    'put' => [
        'controller' => UpdatePostController::class,
        'deserialize' => false,
        'security' => "is_granted('ROLE_ADMIN')",
        'security_message' => 'Only admins can add posts.',
        'input_formats' => [
            'multipart' => ['multipart/form-data'],
        ],
    ],
], output: PostOutput::class)]
#[ORM\Entity(repositoryClass: PostRepository::class)]
class Post
{
    #[
        ORM\Id,
        ORM\Column(type: 'string', unique: true),
        ORM\GeneratedValue(strategy: 'CUSTOM'),
        ORM\CustomIdGenerator(class: 'doctrine.uuid_generator')
    ]
    private string $id;

    #[
        ORM\ManyToOne(targetEntity: User::class, cascade: ['remove'], inversedBy: 'posts'),
        ORM\JoinColumn(name: "user_id", referencedColumnName: "id", nullable: false, onDelete: "CASCADE"),
        Assert\NotNull
    ]
    private User $author;

    #[
        ORM\Column(type: 'string', length: 255, unique: true),
        Assert\Length(min: 10, minMessage: 'Title must be at least {{ limit }} characters long!')
    ]
    private string $title;

    #[
        ORM\Column(type: 'text'),
        Assert\Length(min: 125, minMessage: 'Body should be at least {{ limit }} characters long!')
    ]
    private string $body;

    /**
     * @var string[]
     */
    #[ORM\Column(type: 'array')]
    private array $tags;

    #[ORM\Column(type: 'array')]
    private ArrayCollection $images;

    #[
        ORM\Column(type: 'datetime_immutable'),
    ]
    private DateTimeImmutable $createdAt;

    #[ORM\Column(type: 'datetime_immutable', nullable: true)]
    private ?DateTimeImmutable $updatedAt;

    #[ORM\Column(type: 'datetime_immutable', nullable: true)]
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
