<?php

declare(strict_types=1);

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Delete;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Post;
use App\Repository\AnswerRepository;
use App\State\CityProcessor;
use DateTimeImmutable;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\Entity(repositoryClass: AnswerRepository::class),
    ApiResource(
        normalizationContext: ['groups' => ['answer:read']],
        denormalizationContext: ['groups' => ['answer:write']]
    ),
    GetCollection(),
    Post(
        security: "is_granted('ROLE_ADMIN')",
        securityMessage: 'Only admins can create answer',
        processor: CityProcessor::class,
    ),
    Delete(security: "is_granted('ROLE_ADMIN')", securityMessage: 'Only admins can delete answer')
]
class Answer
{
    #[
        ORM\Id,
        ORM\Column(type: 'string', unique: true),
        ORM\GeneratedValue(strategy: 'CUSTOM'),
        ORM\CustomIdGenerator(class: 'doctrine.uuid_generator'),
        Groups(['answer:read'])
    ]
    private string $id;

    #[
        ORM\ManyToOne(targetEntity: Question::class, inversedBy: 'answer'),
        ORM\JoinColumn(name: 'question_id', referencedColumnName: 'id', onDelete: 'CASCADE'),
        Groups(['answer:read'])
    ]
    private Question $question;

    #[
        ORM\Column(type: 'boolean', length: 255),
        Groups(['answer:read'])
    ]
    private bool $answer;

    #[
        ORM\Column(type: 'datetime_immutable'),
        Groups(['answer:read'])
    ]
    private DateTimeImmutable $createdAt;

    #[ORM\Column(type: 'datetime_immutable', nullable: true), Groups(['answer:read'])]
    private ?DateTimeImmutable $updatedAt = null;

    #[ORM\Column(type: 'datetime_immutable', nullable: true), Groups(['answer:read'])]
    private ?DateTimeImmutable $deletedAt = null;

    #[
        ORM\ManyToOne(targetEntity: Location::class, inversedBy: 'answers'),
        ORM\JoinColumn(name: 'location_id', referencedColumnName: 'id', onDelete: 'CASCADE'),
        Groups(['answer:read'])
    ]
    private Location $location;

    public function __construct()
    {
        $this->createdAt = new DateTimeImmutable('now');
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function getAnswer(): ?bool
    {
        return $this->answer;
    }

    public function setAnswer(bool $answer): self
    {
        $this->answer = $answer;

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

    public function getLocation(): ?Location
    {
        return $this->location;
    }

    public function setLocation(?Location $location): self
    {
        $this->location = $location;

        return $this;
    }

    public function getQuestion(): string
    {
        return $this->question;
    }

    public function setQuestion(string $question): void
    {
        $this->question = $question;
    }
}
