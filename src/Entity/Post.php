<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiResource;
use App\Controller\CreatePostAction;
use App\Controller\UpdatePostController;
use App\DTO\Post\PostInput;
use App\DTO\Post\PostOutput;
use App\Repository\PostRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Validator\Constraints as Assert;


#[ApiResource(collectionOperations: [
    'get',
    'post' => [
        "security" => "is_granted('ROLE_ADMIN')",
        "security_message" => "Only admins can add posts.",
        'input_formats' => [
            'multipart' => ['multipart/form-data'],
        ],
    ],
], itemOperations: [
    'get',
    'put' => [
        "controller" => UpdatePostController::class,
        "security" => "is_granted('ROLE_ADMIN')",
        "security_message" => "Only admins can add posts.",
        'input_formats' => [
            'multipart' => ['multipart/form-data'],
        ],
    ],
], input: PostInput::class, output: PostOutput::class)]
#[ORM\Entity(repositoryClass: PostRepository::class)]
class Post
{
    #[
        ORM\Id,
        ORM\Column(type: 'string', unique: true),
        ORM\GeneratedValue(strategy: "CUSTOM"),
        ORM\CustomIdGenerator(class: "doctrine.uuid_generator")
    ]
    private $id;

    #[
        ORM\ManyToOne(targetEntity: User::class, inversedBy: 'posts'),
        ORM\JoinColumn(nullable: false),
        Assert\NotNull
    ]
    private User $author;

    #[
        ORM\Column(type: 'string', length: 255),
        Assert\Length(min: 10, minMessage: "Title must be at least {{ limit }} characters long!")
    ]
    private string $title;

    #[
        ORM\Column(type: 'text'),
        Assert\Length(min: 125, minMessage: "Body should be at least {{ limit }} characters long!")
    ]
    private string $body;

    #[
        ORM\Column(type: 'array'),
    ]
    private array $tags;

    #[ORM\Column(type: 'array')]
    private array $images;


    #[
        ORM\Column(type: 'datetime_immutable'),
    ]
    private \DateTimeImmutable $createdAt;

    #[ORM\Column(type: 'datetime_immutable', nullable: true)]
    private ?\DateTimeImmutable $updatedAt;

    #[ORM\Column(type: 'datetime_immutable', nullable: true)]
    private ?\DateTimeImmutable $deletedAt;

    public function __construct()
    {
        $this->images = [];
        $this->createdAt = new \DateTimeImmutable("now");
        $this->deletedAt = null;
        $this->updatedAt = null;
    }

    public function getId()
    {
        return $this->id;
    }

    public function getAuthor(): ?User
    {
        return $this->author;
    }

    public function setAuthor(User|UserInterface $author): self
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

    public function getCreatedAt(): ?\DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function getUpdatedAt(): ?\DateTimeImmutable
    {
        return $this->updatedAt;
    }

    public function setUpdatedAt(?\DateTimeImmutable $updatedAt): self
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }

    public function getDeletedAt(): ?\DateTimeImmutable
    {
        return $this->deletedAt;
    }

    public function setDeletedAt(?\DateTimeImmutable $deletedAt): self
    {
        $this->deletedAt = $deletedAt;

        return $this;
    }

    public function getTags(): array
    {
        return $this->tags;
    }

    public function setTags(array $tags): void
    {
        $this->tags = $tags;
    }

    public function getImages(): ArrayCollection|array
    {
        return $this->images;
    }

    public function setImages(ArrayCollection|array $images): void
    {
        $this->images = $images;
    }
}
