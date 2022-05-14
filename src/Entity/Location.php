<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiResource;
use App\Controller\CreateLocationAction;
use App\DTO\Location\LocationOutput;
use App\Repository\LocationRepository;
use DateTimeImmutable;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: LocationRepository::class),
    ApiResource(
        collectionOperations: [
            'get',
            'post' => [
                'controller' => CreateLocationAction::class,
                'deserialize' => false,
                'input_formats' => [
                    'multipart' => ['multipart/form-data'],
                ],
            ]
        ], output: LocationOutput::class
    )]
class Location
{
    #[
        ORM\Id,
        ORM\Column(type: 'string', unique: true),
        ORM\GeneratedValue(strategy: "CUSTOM"),
        ORM\CustomIdGenerator(class: "doctrine.uuid_generator")
    ]
    private $id;

    #[
        ORM\ManyToOne(targetEntity: Category::class, inversedBy: 'locations'),
        ORM\JoinColumn(nullable: false),
        Assert\NotNull
    ]
    private Category $category;

    #[ORM\ManyToOne(targetEntity: User::class, inversedBy: 'locations')]
    private ?User $user;

    #[ORM\OneToMany(mappedBy: 'location', targetEntity: Answer::class)]
    private Collection $answers;

    #[ORM\ManyToOne(targetEntity: City::class, inversedBy: 'locations'),
        ORM\JoinColumn(nullable: false),
        Assert\NotNull
    ]
    private City $city;

    #[
        ORM\Column(type: 'array'),
        Assert\NotBlank(message: 'You need at least 1 image to create Location!')
    ]
    private array $images = [];

    #[
        ORM\Column(type: 'string', length: 255),
        Assert\Length(
            min: 3, max: 32,
            minMessage: "Name must be at least {{ limit }} characters long!",
            maxMessage: "Name must be at most {{ limit }} characters long!",
        )
    ]
    private string $name;

    #[
        ORM\Column(type: 'string', length: 255),
        Assert\NotBlank(message: 'Street address must be provided!')
    ]
    private string $street;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private ?string $phone;

    #[
        ORM\Column(type: 'string', length: 255, nullable: true),
        Assert\Email
    ]
    private ?string $email;

    #[ORM\Column(type: 'boolean')]
    private bool $published = false;

    #[ORM\Column(type: 'boolean')]
    private bool $featured = false;

    #[
        ORM\Column(type: 'text', nullable: true),
        Assert\Length(max: 255, maxMessage: "About field must be at most {{ limit }} characters long!")
    ]
    private ?string $about;

    #[ORM\Column(type: 'datetime_immutable')]
    private \DateTimeImmutable $createdAt;

    #[ORM\Column(type: 'datetime_immutable', nullable: true)]
    private ?\DateTimeImmutable $updatedAt;

    #[ORM\Column(type: 'datetime_immutable', nullable: true)]
    private ?\DateTimeImmutable $deletedAt;

    public function __construct()
    {
        $this->answers = new ArrayCollection();
        $this->createdAt = new DateTimeImmutable("now");
        $this->updatedAt = null;
        $this->deletedAt = null;
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function getCategory(): Category
    {
        return $this->category;
    }

    public function setCategory(Category $category): self
    {
        $this->category = $category;

        return $this;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): self
    {
        $this->user = $user;

        return $this;
    }

    public function getAnswers(): Collection
    {
        return $this->answers;
    }

    public function addAnswer(Answer $answer): self
    {
        if (!$this->answers->contains($answer)) {
            $this->answers[] = $answer;
            $answer->setLocation($this);
        }

        return $this;
    }

    public function removeAnswer(Answer $answer): self
    {
        if ($this->answers->removeElement($answer)) {
            // set the owning side to null (unless already changed)
            if ($answer->getLocation() === $this) {
                $answer->setLocation(null);
            }
        }

        return $this;
    }

    public function getImages(): ?array
    {
        return $this->images;
    }

    public function setImages(array $images): self
    {
        $this->images = $images;

        return $this;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getStreet(): ?string
    {
        return $this->street;
    }

    public function setStreet(string $street): self
    {
        $this->street = $street;

        return $this;
    }

    public function getCity(): ?City
    {
        return $this->city;
    }

    public function setCity(?City $city): self
    {
        $this->city = $city;

        return $this;
    }

    public function getPhone(): ?string
    {
        return $this->phone;
    }

    public function setPhone(?string $phone): self
    {
        $this->phone = $phone;

        return $this;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(?string $email): self
    {
        $this->email = $email;

        return $this;
    }

    public function getPublished(): ?bool
    {
        return $this->published;
    }

    public function setPublished(bool $published): self
    {
        $this->published = $published;

        return $this;
    }

    public function getFeatured(): ?bool
    {
        return $this->featured;
    }

    public function setFeatured(bool $featured): self
    {
        $this->featured = $featured;

        return $this;
    }

    public function getAbout(): ?string
    {
        return $this->about;
    }

    public function setAbout(?string $about): self
    {
        $this->about = $about;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeImmutable $createdAt): self
    {
        $this->createdAt = $createdAt;

        return $this;
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
}