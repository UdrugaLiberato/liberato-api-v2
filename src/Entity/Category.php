<?php

declare(strict_types=1);

namespace App\Entity;

use ApiPlatform\Metadata\ApiProperty;
use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Delete;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Put;
use App\Controller\CreateCategoryController;
use App\Controller\UpdateCategoryController;
use App\DTO\Category\CategoryInput;
use App\Repository\CategoryRepository;
use App\State\CategoryProcessor;
use DateTimeImmutable;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: CategoryRepository::class),
    ApiResource(normalizationContext: ['groups' => ['category:read', 'location:read']],
        denormalizationContext: ['groups' => ['category:write']]),
    GetCollection(),
\ApiPlatform\Metadata\Post(
    uriTemplate: '/categories',
    inputFormats: ['multipart' => ['multipart/form-data']],
    controller: CreateCategoryController::class,
    security: 'is_granted
("ROLE_ADMIN")',
    securityMessage: 'Only admins can access this resource',
    deserialize: false,
    name: "category",
    ),
    Get(),
    Delete(security: "is_granted('ROLE_ADMIN')", securityMessage: 'Only admins can delete posts.',),
    Put(
        inputFormats: ['multipart' => ['multipart/form-data']],
        controller: UpdateCategoryController::class,
        security: "is_granted('ROLE_ADMIN')",
        securityMessage: 'Only admins can update posts.',
        deserialize: false
    )]
class Category
{
    #[
        ApiProperty(identifier: true),
        ORM\Id,
        ORM\Column(type: 'string', unique: true),
        ORM\GeneratedValue(strategy: 'CUSTOM'),
        ORM\CustomIdGenerator(class: 'doctrine.uuid_generator'),
        Groups(['category:read', 'category:write'])
    ]
    private string $id;

    #[
        ORM\Column(type: 'string', length: 255, unique: true),
        Assert\Length(min: 3, minMessage: 'Name must be at least {{ limit }} characters long!'),
        Groups(['category:read', 'category:write'])
    ]
    private string $name;

    #[
        ORM\Column(type: 'object', nullable: true),
        Groups(['category:read', 'category:write'])
    ]
    private ArrayCollection $icon;

    #[
        ORM\Column(type: 'text', nullable: true),
        Assert\NotNull,
        Assert\Length(min: 5, minMessage: 'Description must be at least {{ limit }} characters long!'),
        Groups(['category:read', 'category:write'])
    ]
    private ?string $description;

    #[ORM\Column(type: 'datetime_immutable'), Groups(['category:read'])]
    private DateTimeImmutable $createdAt;

    #[ORM\Column(type: 'datetime_immutable', nullable: true), Groups(['category:read'])]
    private ?DateTimeImmutable $updatedAt = null;

    #[ORM\Column(type: 'datetime_immutable', nullable: true), Groups(['category:read'])]
    private ?DateTimeImmutable $deletedAt = null;

    #[
        ORM\OneToMany(mappedBy: 'category', targetEntity: Question::class, cascade: ['remove']),
        Groups(['category:read'])]
    private ?Collection $questions;

    #[ORM\OneToMany(mappedBy: 'category', targetEntity: Location::class),
        Groups(['category:read'])]
    private Collection $locations;

    public function __construct()
    {
        $this->createdAt = new DateTimeImmutable('now');
        $this->questions = new ArrayCollection();
        $this->locations = new ArrayCollection();
        $this->icon = new ArrayCollection();
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getIcon(): ?ArrayCollection
    {
        return $this->icon;
    }

    public function setIcon(ArrayCollection $icon): self
    {
        $this->icon = $icon;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): self
    {
        $this->description = $description;

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

    /**
     * @return Collection<int, Question>
     */
    public function getQuestions(): Collection
    {
        return $this->questions;
    }

    public function addQuestion(Question $question): self
    {
        if (!$this->questions->contains($question)) {
            $this->questions[] = $question;
            $question->setCategory($this);
        }

        return $this;
    }

    public function removeQuestion(Question $question): self
    {
        if ($this->questions->removeElement($question)) {
            // set the owning side to null (unless already changed)
            if ($question->getCategory() === $this) {
                $question->setCategory(null);
            }
        }

        return $this;
    }

    public function getLocations(): ArrayCollection
    {
        return new ArrayCollection($this->locations->toArray());
    }

    public function addLocation(Location $location): self
    {
        if (!$this->locations->contains($location)) {
            $this->locations[] = $location;
            $location->setCategory($this);
        }

        return $this;
    }

    public function removeLocation(Location $location): self
    {
        if ($this->locations->removeElement($location)) {
            // set the owning side to null (unless already changed)
            if ($location->getCategory() === $this) {
                $location->setCategory(null);
            }
        }

        return $this;
    }
}
