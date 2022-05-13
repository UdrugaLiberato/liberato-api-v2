<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiResource;
use App\DTO\City\CityInput;
use App\DTO\City\CityOutput;
use App\Repository\CityRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: CityRepository::class),
    ApiResource(collectionOperations: [
        "post" => [
            "security" => "is_granted('ROLE_ADMIN')",
            "security_message" => "Only admins can add posts.",
        ]], input: CityInput::class, output: CityOutput::class)]
class City
{
    #[
        ORM\Id,
        ORM\Column(type: 'string', unique: true),
        ORM\GeneratedValue(strategy: "CUSTOM"),
        ORM\CustomIdGenerator(class: "doctrine.uuid_generator")
    ]
    private $id;

    #[
        ORM\Column(type: 'string', length: 255, unique: true, nullable: false),
        Assert\Length(
            min: 3,
            minMessage: "Name field must be at {{ limit }} characters long!"
        )
    ]
    private string $name;

    #[
        ORM\Column(type: 'float', nullable: false),
        Assert\Range(
            notInRangeMessage: "Your latitude must be between {{ min }} and {{ max }} deg.",
            min: -90,
            max: 90,
        )
    ]
    private float $latitude;

    #[
        ORM\Column(type: 'float', nullable: false),
        Assert\Range(
            notInRangeMessage: "Your longitude must be between {{ min }} and {{ max }} deg.",
            min: -180,
            max: 180,
        )
    ]
    private float $longitude;

    #[ORM\Column(type: 'datetime_immutable')]
    private \DateTimeImmutable $createdAt;

    #[ORM\Column(type: 'datetime_immutable', nullable: true)]
    private ?\DateTimeImmutable $updatedAt = null;

    #[ORM\Column(type: 'datetime_immutable', nullable: true)]
    private ?\DateTimeImmutable $deletedAt = null;

    public function __construct()
    {
        $this->createdAt = new \DateTimeImmutable("now");
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

    public function getCreatedAt(): \DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeImmutable $createdAt): void
    {
        $this->createdAt = $createdAt;
    }

    public function getUpdatedAt(): ?\DateTimeImmutable
    {
        return $this->updatedAt;
    }

    public function setUpdatedAt(?\DateTimeImmutable $updatedAt): void
    {
        $this->updatedAt = $updatedAt;
    }

    public function getDeletedAt(): ?\DateTimeImmutable
    {
        return $this->deletedAt;
    }

    public function setDeletedAt(?\DateTimeImmutable $deletedAt): void
    {
        $this->deletedAt = $deletedAt;
    }
}
