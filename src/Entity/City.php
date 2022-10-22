<?php

declare(strict_types=1);

namespace App\Entity;

use ApiPlatform\Metadata\ApiProperty;
use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Delete;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Post;
use ApiPlatform\Metadata\Put;
use App\Controller\UpdateCityController;
use App\Repository\CityRepository;
use App\State\CityProcessor;
use App\State\CityProvider;
use DateTimeImmutable;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Uid\Uuid;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: CityRepository::class),
    ApiResource(normalizationContext: ['groups' => ['city:read']],
        denormalizationContext: ['groups' => ['city:write']],
        provider: CityProvider::class),
    GetCollection(),
    Get(),
    Post(
        security: "is_granted('ROLE_ADMIN')",
        securityMessage: 'Only admins can create cities',
        processor: CityProcessor::class,
    ),
    Delete(security: "is_granted('ROLE_ADMIN')", securityMessage: 'Only admins can delete cities'),
    Put(
        controller: UpdateCityController::class,
        security: "is_granted('ROLE_ADMIN')",
        securityMessage: 'Only admins can update cities'
    )]
class City
{
    #[
        ApiProperty(identifier: true),
        ORM\Id,
        ORM\Column(type: 'string', unique: true),
        ORM\GeneratedValue(strategy: 'CUSTOM'),
        ORM\CustomIdGenerator(class: 'doctrine.uuid_generator'),
        Groups(['city:read'])
    ]
    private ?string $id = null;

    #[
        ORM\Column(type: 'string', length: 255, unique: true, nullable: false),
        Assert\Length(
            min: 3,
            minMessage: 'Name field must be at {{ limit }} characters long!'
        ),
        Groups(['city:read', 'city:write'])
    ]
    private string $name;

    #[
        ORM\Column(type: 'float', nullable: false),
        Assert\Range(
            notInRangeMessage: 'Your latitude must be between {{ min }} and {{ max }} deg.',
            min: -90,
            max: 90,
        ),
        Groups(['city:read'])
    ]
    private float $latitude;

    #[
        ORM\Column(type: 'float', nullable: false),
        Assert\Range(
            notInRangeMessage: 'Your longitude must be between {{ min }} and {{ max }} deg.',
            min: -180,
            max: 180,
        ),
        Groups(['city:read'])
    ]
    private float $longitude;

    #[
        ORM\Column(type: 'datetime_immutable'),
        Groups(['city:read'])
    ]
    private DateTimeImmutable $createdAt;

    #[
        ORM\Column(type: 'datetime_immutable', nullable: true),
        Groups(['city:read'])
    ]
    private ?DateTimeImmutable $updatedAt = null;

    #[
        ORM\Column(type: 'datetime_immutable', nullable: true),
        Groups(['city:read'])
    ]
    private ?DateTimeImmutable $deletedAt = null;

    #[
        ORM\OneToMany(mappedBy: 'city', targetEntity: Location::class, orphanRemoval: true),
        Groups(['city:read'])
    ]
    private Collection $locations;

    public function __construct()
    {
        $this->locations = new ArrayCollection();
        $this->createdAt = new DateTimeImmutable('now');
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

    public function getLatitude(): float
    {
        return $this->latitude;
    }

    public function setLatitude(float $latitude): self
    {
        $this->latitude = $latitude;

        return $this;
    }

    public function getLongitude(): float
    {
        return $this->longitude;
    }

    public function setLongitude(float $longitude): self
    {
        $this->longitude = $longitude;

        return $this;
    }

    public function getCreatedAt(): string
    {
        return $this->createdAt->format('Y-m-d H:i:s');
    }

    public function setCreatedAt(DateTimeImmutable $createdAt): void
    {
        $this->createdAt = $createdAt;
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

    public function getLocations(): ArrayCollection
    {
        return new ArrayCollection($this->locations->toArray());
    }

    public function addLocation(Location $location): self
    {
        if (!$this->locations->contains($location)) {
            $this->locations[] = $location;
            $location->setCity($this);
        }

        return $this;
    }

    public function removeLocation(Location $location): self
    {
        if ($this->locations->removeElement($location)) {
            // set the owning side to null (unless already changed)
            if ($location->getCity() === $this) {
                $location->setCity(null);
            }
        }

        return $this;
    }
}
