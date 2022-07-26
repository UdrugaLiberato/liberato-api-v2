<?php

declare(strict_types=1);

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiResource;
use App\DTO\Category\CategoryInput;
use App\DTO\Category\CategoryOutput;
use App\Repository\CategoryRepository;
use DateTimeImmutable;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: CategoryRepository::class),
    ApiResource(collectionOperations: [
        'get',
        'post' => [
            'input' => CategoryInput::class,
            'security' => "is_granted('ROLE_ADMIN')",
            'security_message' => 'Only admins can add posts.',
            'input_formats' => [
                'multipart' => ['multipart/form-data'],
            ],
        ],
    ], output: CategoryOutput::class)]
class Category
{
    #[
        ORM\Id,
        ORM\Column(type: 'string', unique: true),
        ORM\GeneratedValue(strategy: 'CUSTOM'),
        ORM\CustomIdGenerator(class: 'doctrine.uuid_generator')
    ]
    private string $id;

    #[
        ORM\Column(type: 'string', length: 255, unique: true),
        Assert\Length(min: 3, minMessage: 'Name must be at least {{ limit }} characters long!')
    ]
    private string $name;

    #[ORM\Column(type: 'array', nullable: true)]
    private ?ArrayCollection $icon;

    #[
        ORM\Column(type: 'text', nullable: true),
        Assert\Length(min: 5, minMessage: 'Description must be at least {{ limit }} characters long!')
    ]
    private ?string $description;

    #[ORM\Column(type: 'datetime_immutable')]
    private DateTimeImmutable $createdAt;

    #[ORM\Column(type: 'datetime_immutable', nullable: true)]
    private ?DateTimeImmutable $updatedAt = null;

    #[ORM\Column(type: 'datetime_immutable', nullable: true)]
    private ?DateTimeImmutable $deletedAt = null;

    #[ORM\OneToMany(mappedBy: 'Category', targetEntity: Question::class)]
    private ArrayCollection $questions;

    #[ORM\OneToMany(mappedBy: 'category', targetEntity: Location::class)]
    private ArrayCollection $locations;

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
        return $this->locations;
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
