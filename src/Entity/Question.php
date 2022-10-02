<?php

declare(strict_types=1);

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\GetCollection;
use App\DTO\Question\QuestionInput;
use App\DTO\Question\QuestionOutput;
use App\Repository\QuestionRepository;
use DateTimeImmutable;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: QuestionRepository::class),
    ApiResource(input: QuestionInput::class, output: QuestionOutput::class),
    GetCollection(),
    \ApiPlatform\Metadata\Post(
        security: 'is_granted("ROLE_ADMIN")',
        securityMessage: 'Only admins can create questions',
    ),
]
class Question
{
    #[
        ORM\Id,
        ORM\Column(type: 'string', unique: true),
        ORM\GeneratedValue(strategy: 'CUSTOM'),
        ORM\CustomIdGenerator(class: 'doctrine.uuid_generator')
    ]
    private string $id;

    #[
        ORM\ManyToOne(targetEntity: Category::class, cascade: ['remove'], inversedBy: 'questions'),
        ORM\JoinColumn(
            name: 'category_id',
            referencedColumnName: 'id',
            nullable: false,
            onDelete: 'CASCADE'
        ),
        Assert\NotNull
    ]
    private Category $category;

    #[
        ORM\Column(type: 'string', length: 255),
        Assert\Length(min: 5, minMessage: 'Question must be at least {{ limit }} characters long!')
    ]
    private string $question;

    #[ORM\Column(type: 'datetime_immutable')]
    private DateTimeImmutable $createdAt;

    #[ORM\Column(type: 'datetime_immutable', nullable: true)]
    private ?DateTimeImmutable $updatedAt = null;

    #[ORM\Column(type: 'datetime_immutable', nullable: true)]
    private ?DateTimeImmutable $deletedAt = null;

    public function __construct()
    {
        $this->createdAt = new DateTimeImmutable('now');
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function getCategory(): ?Category
    {
        return $this->category;
    }

    public function setCategory(?Category $category): self
    {
        $this->category = $category;

        return $this;
    }

    public function getQuestion(): ?string
    {
        return $this->question;
    }

    public function setQuestion(string $question): self
    {
        $this->question = $question;

        return $this;
    }

    public function getCreatedAt(): DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function getUpdatedAt(): ?DateTimeImmutable
    {
        return $this->updatedAt;
    }

    public function setUpdatedAt(?DateTimeImmutable $updatedAt): void
    {
        $this->updatedAt = $updatedAt;
    }

    public function getDeletedAt(): ?DateTimeImmutable
    {
        return $this->deletedAt;
    }

    public function setDeletedAt(?DateTimeImmutable $deletedAt): void
    {
        $this->deletedAt = $deletedAt;
    }
}
