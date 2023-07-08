<?php

namespace App\Entity;

use ApiPlatform\Doctrine\Odm\Filter\ExistsFilter;
use ApiPlatform\Metadata\ApiFilter;
use ApiPlatform\Metadata\ApiProperty;
use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use App\Repository\VolunteerRepository;
use App\State\VolunteerProvider;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\Entity(repositoryClass: VolunteerRepository::class),
    ApiResource(
        normalizationContext: ['groups' => ['volunteer:read']],
        denormalizationContext: ['groups' => ['volunteer:write']],
        paginationEnabled: false,
        provider: VolunteerProvider::class
    ),
    GetCollection(),
    Get(), ]
class Volunteer {
  #[
      ApiProperty(identifier: true),
      ORM\Id,
      ORM\Column(type: 'string', unique: true),
      ORM\GeneratedValue(strategy: 'CUSTOM'),
      ORM\CustomIdGenerator(class: 'doctrine.uuid_generator')]
  private ?int $id = NULL;

  #[ORM\Column(length: 255)]
  private ?string $firstName = NULL;

  #[ORM\Column(length: 255)]
  private ?string $lastName = NULL;

  #[ORM\Column(length: 255)]
  private ?string $city = NULL;

  #[ORM\Column(length: 255)]
  private ?string $email = NULL;

  #[ORM\Column]
  private ?bool $isMember = NULL;

  #[ORM\Column(type: 'text')]
  private ?string $reason = NULL;

  #[ORM\Column(type: 'array'), Groups(['volunteer:read'])]
  private ArrayCollection $resume;

  #[
      ORM\Column(type: 'datetime_immutable'),
      Groups(['volunteer:read'])
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

  public function __construct() {
    $this->createdAt = new \DateTimeImmutable('now');
  }

  public function getId(): ?int {
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

  public function isIsMember(): ?bool {
    return $this->isMember;
  }

  public function setIsMember(bool $isMember): static {
    $this->isMember = $isMember;

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

  /**
   * @return ArrayCollection
   */
  public function getResume(): ArrayCollection {
    return $this->resume;
  }

  /**
   * @param ArrayCollection $resume
   */
  public function setResume(ArrayCollection $resume): void {
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

}