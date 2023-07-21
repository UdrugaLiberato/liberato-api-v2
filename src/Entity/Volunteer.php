<?php

namespace App\Entity;

use ApiPlatform\Doctrine\Odm\Filter\ExistsFilter;
use ApiPlatform\Metadata\ApiFilter;
use ApiPlatform\Metadata\ApiProperty;
use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Post;
use App\DTO\Volunteer\VolunteerInput;
use App\Repository\VolunteerRepository;
use App\State\VolunteerProcessor;
use App\State\VolunteerProvider;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: VolunteerRepository::class),
    ApiResource(
        normalizationContext: ['groups' => ['volunteer:read']],
        paginationEnabled: false,
        provider: VolunteerProvider::class
    ),
    GetCollection(),
    Get(),
    Post(
        inputFormats: ['multipart' => ['multipart/form-data']],
        input: VolunteerInput::class,
        processor: VolunteerProcessor::class,
    ),
]
class Volunteer {
  #[
      ApiProperty(identifier: true),
      ORM\Id,
      ORM\Column(type: 'string', unique: true),
      ORM\GeneratedValue(strategy: 'CUSTOM'),
      ORM\CustomIdGenerator(class: 'doctrine.uuid_generator'),
      Groups(['volunteer:read'])
  ]
  private ?string $id = NULL;

  #[
      ORM\Column(length: 255),
      Assert\Length(min: 2, max: 255, minMessage: 'Your first name must be at least {{ limit }} characters long'),
      Groups(['volunteer:read'])
  ]
  private ?string $firstName = NULL;

  #[
      ORM\Column(length: 255),
      Assert\Length(min: 2, max: 255, minMessage: 'Your last name must be at least {{ limit }} characters long'),
      Groups(['volunteer:read'])
  ]
  private ?string $lastName = NULL;

  #[
      ORM\Column(length: 255),
      Assert\Length(min: 2, max: 255, minMessage: 'Your city must be at least {{ limit }} characters long'),
      Groups(['volunteer:read'])
  ]
  private ?string $city = NULL;

  #[
      ORM\Column(length: 255),
      Assert\Email(message: 'The email {{ value }} is not a valid email.'),
      Groups(['volunteer:read'])
  ]
  private ?string $email = NULL;

  #[
      ORM\Column,
      Groups(['volunteer:read'])
  ]
  private ?bool $membership = NULL;

  #[
      ORM\Column(type: 'text'),
      Assert\Length(min: 10, max: 2000, minMessage: 'Your reason must be at least {{ limit }} characters long', maxMessage: 'Your reason must be at 
      most {{ limit }} characters long'),
      Groups(['volunteer:read'])
  ]
  private ?string $reason = NULL;

  #[
      ORM\Column(type: 'array'),
      Groups(['volunteer:read'])
  ]
  private array $resume;

  #[
      ORM\Column(type: 'datetime_immutable'),
      Groups(['volunteer:read']),
  ]
  private \DateTimeImmutable $createdAt;

  #[
      ORM\Column(type: 'datetime_immutable', nullable: true),
      Groups(['volunteer:read'])
  ]
  private ?\DateTimeImmutable $updatedAt = NULL;

  #[
      ORM\Column(type: 'datetime_immutable', nullable: true),
      Groups(['volunteer:read']),
      ApiFilter(ExistsFilter::class)
  ]
  private ?\DateTimeImmutable $deletedAt = NULL;

  #[ORM\Column(type: Types::TEXT, nullable: true)]
  private ?string $notes = null;

  public function __construct() {
    $this->createdAt = new \DateTimeImmutable('now');
  }

  public function getId(): ?string {
    return $this->id;
  }

  public function getFirstName(): ?string {
    return $this->firstName;
  }

  public function setFirstName(string $firstName): static {
    $this->firstName = $firstName;

    return $this;
  }

  public function getLastName(): ?string {
    return $this->lastName;
  }

  public function setLastName(string $lastName): static {
    $this->lastName = $lastName;

    return $this;
  }

  public function getCity(): ?string {
    return $this->city;
  }

  public function setCity(string $city): static {
    $this->city = $city;

    return $this;
  }

  public function getEmail(): ?string {
    return $this->email;
  }

  public function setEmail(string $email): static {
    $this->email = $email;

    return $this;
  }

  public function isMembership(): ?bool {
    return $this->membership;
  }

  public function setMembership(bool $isMember): static {
    $this->membership = $isMember;

    return $this;
  }

  /**
   * @return string|null
   */
  public function getReason(): ?string {
    return $this->reason;
  }

  /**
   * @param string|null $reason
   */
  public function setReason(?string $reason): void {
    $this->reason = $reason;
  }

  public function getResume(): array {
    return $this->resume;
  }
  public function setResume(array $resume): void {
    $this->resume = $resume;
  }

  /**
   * @return \DateTimeImmutable
   */
  public function getCreatedAt(): \DateTimeImmutable {
    return $this->createdAt;
  }

  /**
   * @param \DateTimeImmutable $createdAt
   */
  public function setCreatedAt(\DateTimeImmutable $createdAt): void {
    $this->createdAt = $createdAt;
  }

  /**
   * @return \DateTimeImmutable|null
   */
  public function getUpdatedAt(): ?\DateTimeImmutable {
    return $this->updatedAt;
  }

  /**
   * @param \DateTimeImmutable|null $updatedAt
   */
  public function setUpdatedAt(?\DateTimeImmutable $updatedAt): void {
    $this->updatedAt = $updatedAt;
  }

  /**
   * @return \DateTimeImmutable|null
   */
  public function getDeletedAt(): ?\DateTimeImmutable {
    return $this->deletedAt;
  }

  /**
   * @param \DateTimeImmutable|null $deletedAt
   */
  public function setDeletedAt(?\DateTimeImmutable $deletedAt): void {
    $this->deletedAt = $deletedAt;
  }

  public function getNotes(): ?string
  {
      return $this->notes;
  }

  public function setNotes(?string $notes): static
  {
      $this->notes = $notes;

      return $this;
  }

}