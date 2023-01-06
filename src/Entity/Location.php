<?php

declare(strict_types=1);

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiFilter;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\SearchFilter;
use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Delete;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Post;
use ApiPlatform\Metadata\Put;
use App\DTO\Location\LocationInput;
use App\Repository\LocationRepository;
use App\State\CreateLocationProcessor;
use App\State\LocationProvider;
use Cloudinary\Transformation\X;
use DateTimeImmutable;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

#[
    ORM\Entity(repositoryClass: LocationRepository::class),
    ApiResource(
        normalizationContext: ['groups' => ['location:read']],
    ),
    GetCollection(provider: LocationProvider::class),
    Post(
        inputFormats: ['multipart' => ['multipart/form-data']],
        security: "is_granted('ROLE_ADMIN')",
        securityMessage: 'Only admins can create locations',
        input: LocationInput::class,
        processor: CreateLocationProcessor::class,
    ),
    Get(),
    Put(security: "is_granted('ROLE_ADMIN')", securityMessage: 'Only admins can edit locations'),
    Delete(security: "is_granted('ROLE_ADMIN')", securityMessage: 'Only admins can delete locations'), ]
class Location
{
    #[
        ORM\Id,
        ORM\Column(type: 'string', unique: true),
        ORM\GeneratedValue(strategy: 'CUSTOM'),
        ORM\CustomIdGenerator(class: 'doctrine.uuid_generator'),
    ]
    private string $id;

    #[
        ORM\ManyToOne(targetEntity: Category::class, inversedBy: 'locations'),
        ORM\JoinColumn(nullable: false),
        Assert\NotNull,
        Groups(['location:read'])
    ]
    private Category $category;

    #[ORM\ManyToOne(targetEntity: User::class, inversedBy: 'locations')]
    private ?User $user;

    #[ORM\OneToMany(
        mappedBy: 'location',
        targetEntity: Answer::class,
        cascade: ['persist', 'remove'],
        orphanRemoval: true
    )]
    private $answers;

    #[
        ApiFilter(SearchFilter::class, strategy: 'ipartial'),
        ORM\ManyToOne(targetEntity: City::class, inversedBy: 'locations'),
        ORM\JoinColumn(nullable: false),
        Assert\NotNull,
        Groups(['location:read'])
    ]
    private City $city;

    #[
        Groups(['location:read']),
        ApiFilter(SearchFilter::class, strategy: 'ipartial'),
        ORM\Column(type: 'string', length: 255),
        Assert\Length(
            min: 3,
            max: 32,
            minMessage: 'Name must be at least {{ limit }} characters long!',
            maxMessage: 'Name must be at most {{ limit }} characters long!',
        )
    ]
    private string $name;

    #[
        Groups(['location:read']),
        ORM\Column(type: 'string', length: 255),
        Assert\NotBlank(message: 'Street address must be provided!')
    ]
    private string $street;

    #[ORM\Column(type: 'string', length: 255, nullable: true),
        Groups(['location:read']),
    ]
    private ?string $phone;

    #[
        Groups(['location:read']),
        ORM\Column(type: 'string', length: 255, nullable: true),
    ]
    private ?string $email;

    #[ORM\Column(type: 'boolean'),
        Groups(['location:read']),
    ]
    private bool $published = false;

    #[ORM\Column(type: 'boolean'),
        Groups(['location:read']),
    ]
    private bool $featured = false;

    #[
        Groups(['location:read']),
        ORM\Column(type: 'text', nullable: true),
        Assert\Length(max: 255, maxMessage: 'About field must be at most {{ limit }} characters long!')
    ]
    private ?string $about;

    #[
        Groups(['location:read']),
        ORM\Column(type: 'float', nullable: false),
        Assert\Range(
            notInRangeMessage: 'Your latitude must be between {{ min }} and {{ max }} deg.',
            min: -90,
            max: 90,
        )
    ]
    private float $latitude;

    #[
        Groups(['location:read']),
        ORM\Column(type: 'float', nullable: false),
        Assert\Range(
            notInRangeMessage: 'Your longitude must be between {{ min }} and {{ max }} deg.',
            min: -180,
            max: 180,
        )
    ]
    private float $longitude;

    #[Groups(['location:read']), ORM\Column(type: 'datetime_immutable')]
    private DateTimeImmutable $createdAt;

    #[Groups(['location:read']), ORM\Column(type: 'datetime_immutable', nullable: true)]
    private ?DateTimeImmutable $updatedAt;

    #[Groups(['location:read']), ORM\Column(type: 'datetime_immutable', nullable: true)]
    private ?DateTimeImmutable $deletedAt;

    #[ORM\ManyToMany(targetEntity: Image::class, mappedBy: 'location', cascade: ['persist']), Groups
    (['location:read'])]
    private Collection $images;

    public function __construct()
    {
        $this->answers = new ArrayCollection();
        $this->createdAt = new DateTimeImmutable('now');
        $this->updatedAt = null;
        $this->deletedAt = null;
        $this->images = new ArrayCollection();
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function getCategory(): Category
    {
        return $this->category;
    }

    public function setCategory(?Category $category): self
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

    #[
        Groups(['location:read'])
    ]
public function getQuestionsAndAnswers(): array
    {
        $arr = [];
        foreach ($this->answers as $answer) {
            $arr[] = [
                'question' => $answer->getQuestion()->getQuestion(),
                'answer' => $answer->getAnswer(),
            ];
        }
        return  $arr;
}

    public
    function addAnswer(Answer $answer): self
    {
        if (!$this->answers->contains($answer)) {
            $this->answers[] = $answer;
            $answer->setLocation($this);
        }

        return $this;
    }

    public
    function removeAnswer(Answer $answer): self
    {
        if ($this->answers->removeElement($answer)) {
            // set the owning side to null (unless already changed)
            if ($answer->getLocation() === $this) {
                $answer->setLocation(null);
            }
        }

        return $this;
    }

    public
    function getName(): ?string
    {
        return $this->name;
    }

    public
    function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public
    function getLatitude(): float
    {
        return $this->latitude;
    }

    public
    function setLatitude(float $latitude): void
    {
        $this->latitude = $latitude;
    }

    public
    function getLongitude(): float
    {
        return $this->longitude;
    }

    public
    function setLongitude(float $longitude): void
    {
        $this->longitude = $longitude;
    }

    public
    function getStreet(): ?string
    {
        return $this->street;
    }

    public
    function setStreet(string $street): self
    {
        $this->street = $street;

        return $this;
    }

    public
    function getCity(): ?City
    {
        return $this->city;
    }

    public
    function setCity(?City $city): self
    {
        $this->city = $city;

        return $this;
    }

    public
    function getPhone(): ?string
    {
        return $this->phone;
    }

    public
    function setPhone(?string $phone): self
    {
        $this->phone = $phone;

        return $this;
    }

    public
    function getEmail(): ?string
    {
        return $this->email;
    }

    public
    function setEmail(?string $email): self
    {
        $this->email = $email;

        return $this;
    }

    public
    function getPublished(): ?bool
    {
        return $this->published;
    }

    public
    function setPublished(bool $published): self
    {
        $this->published = $published;

        return $this;
    }

    public
    function getFeatured(): ?bool
    {
        return $this->featured;
    }

    public
    function setFeatured(bool $featured): self
    {
        $this->featured = $featured;

        return $this;
    }

    public
    function getAbout(): ?string
    {
        return $this->about;
    }

    public
    function setAbout(?string $about): self
    {
        $this->about = $about;

        return $this;
    }

    public
    function getCreatedAt(): ?DateTimeImmutable
    {
        return $this->createdAt;
    }

    public
    function setCreatedAt(DateTimeImmutable $createdAt): self
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    public
    function getUpdatedAt(): ?DateTimeImmutable
    {
        return $this->updatedAt;
    }

    public
    function setUpdatedAt(?DateTimeImmutable $updatedAt): self
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }

    public
    function getDeletedAt(): ?DateTimeImmutable
    {
        return $this->deletedAt;
    }

    public
    function setDeletedAt(?DateTimeImmutable $deletedAt): self
    {
        $this->deletedAt = $deletedAt;

        return $this;
    }

    /**
     * @return Collection<int, Image>
     */
    public function getImages(): Collection
    {
        return $this->images;
    }

    public function addImage(Image $image): self
    {
        if (!$this->images->contains($image)) {
            $this->images->add($image);
            $image->addLocation($this);
        }

        return $this;
    }

    public function removeImage(Image $image): self
    {
        if ($this->images->removeElement($image)) {
            $image->removeLocation($this);
        }

        return $this;
    }
}
