<?php

declare(strict_types=1);

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiResource;
use App\Controller\UpdateNewsArticleController;
use App\DTO\NewsArticle\NewsArticleInput;
use App\DTO\NewsArticle\NewsArticleOutput;
use App\Repository\NewsArticleRepository;
use DateTimeImmutable;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[
    ORM\Entity(repositoryClass: NewsArticleRepository::class),
    ApiResource(collectionOperations: [
        'get',
        'post' => [
            'security' => "is_granted('ROLE_ADMIN')",
            'security_message' => 'Only admin users are allowed to add news articles.',
            'input_formats' => [
                'multipart' => ['multipart/form-data'],
            ],
        ],
    ], itemOperations: ['get', 'put' => [
        'security' => "is_granted('ROLE_ADMIN')",
        'deserialize' => false,
        'controller' => UpdateNewsArticleController::class,
        'security_message' => 'Only admin users are allowed to edit news articles.',
        'input_formats' => [
            'multipart' => ['multipart/form-data'],
        ],
    ]], input: NewsArticleInput::class, output: NewsArticleOutput::class)]
class NewsArticle
{
    #[
        ORM\Id,
        ORM\Column(type: 'string', unique: true),
        ORM\GeneratedValue(strategy: 'CUSTOM'),
        ORM\CustomIdGenerator(class: 'doctrine.uuid_generator')
    ]
    private string $id;

    #[
        ORM\Column(type: 'string', length: 255),
        Assert\NotBlank(message: 'Title is required.')
    ]
    private string $title;

    #[
        ORM\Column(type: 'string', length: 255),
        Assert\NotBlank(message: 'URL is required.'),
        Assert\Url(message: 'URL is not valid.')
    ]
    private string $url;

    #[ORM\Column(type: 'array', nullable: true)]
    private ArrayCollection $image;

    #[ORM\Column(type: 'datetime_immutable')]
    private DateTimeImmutable $createdAt;

    #[ORM\Column(type: 'datetime_immutable', length: 255, nullable: true)]
    private ?DateTimeImmutable $updatedAt;

    #[ORM\Column(type: 'datetime_immutable', nullable: true)]
    private ?DateTimeImmutable $deletedAt;

    public function __construct()
    {
        $this->createdAt = new DateTimeImmutable('now');
        $this->updatedAt = null;
        $this->deletedAt = null;
        $this->image = new ArrayCollection();
    }

    public function getId(): ?string
    {
        return $this->id;
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

    public function getImage(): ArrayCollection
    {
        return $this->image;
    }

    public function setImage(ArrayCollection $image): void
    {
        $this->image = $image;
    }

    public function getUrl(): ?string
    {
        return $this->url;
    }

    public function setUrl(string $url): self
    {
        $this->url = $url;

        return $this;
    }

    public function getCreatedAt(): ?DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function setCreatedAt(DateTimeImmutable $createdAt): self
    {
        $this->createdAt = $createdAt;

        return $this;
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

    public function setDeletedAt(?DateTimeImmutable $deletedAt): self
    {
        $this->deletedAt = $deletedAt;

        return $this;
    }
}
