<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiProperty;
use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Delete;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Post;
use ApiPlatform\Metadata\Put;
use App\Repository\MemberRepository;
use App\State\MemberProvider;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: MemberRepository::class),
    ApiResource(
        normalizationContext: ['groups' => ['member:read']],
        denormalizationContext: ['groups' => ['member:write']],
        provider: MemberProvider::class),
    GetCollection(),
    Get(),
    Delete(),
    Post(),
    Put(),
]
class Member {
  #[
      ApiProperty(identifier: true),
      ORM\Id,
      ORM\Column(type: 'string', unique: true),
      ORM\GeneratedValue(strategy: 'CUSTOM'),
      ORM\CustomIdGenerator(class: 'doctrine.uuid_generator'),
      Groups(['member:read'])
  ]
  private ?string $id = NULL;

  #[
      ORM\Column(length: 255),
      Groups(['member:read', 'member:write'])
  ]
  private ?string $firstname = NULL;

  #[
      ORM\Column(length: 255, nullable: true),
      Groups(['member:read', 'member:write'])
  ]
  private ?string $lastname = NULL;

  #[
      ORM\Column,
      Groups(['member:read', 'member:write'])
  ]
  private ?bool $isStudent = NULL;

  #[
      ORM\Column(type: Types::DATE_IMMUTABLE),
      Groups(['member:read', 'member:write'])
  ]
  private ?\DateTimeImmutable $dob = NULL;

  #[
      ORM\Column(length: 11, unique: true),
      Groups(['member:read', 'member:write']),
    Assert\Length(exactly: 11, exactMessage: 'OIB must be exactly {{ limit }} characters long'),
  ]
  private ?string $OIB = NULL;

  #[
      ORM\Column(length: 255),
      Groups(['member:read', 'member:write'])
  ]
  private ?string $address = NULL;

  #[
      ORM\Column(length: 255),
      Groups(['member:read', 'member:write'])
  ]
  private ?string $city = NULL;

  #[
      ORM\Column(length: 255, nullable: true),
      Groups(['member:read', 'member:write'])
  ]
  private ?string $phone = NULL;

  #[
      ORM\Column(length: 255, nullable: true),
      Groups(['member:read', 'member:write'])
  ]
  private ?string $email = NULL;

  #[
      ORM\Column(length: 3),
      Groups(['member:read', 'member:write']),
      Assert\Range(
          notInRangeMessage: 'Your disabled percent must be between {{ min }} and {{ max }} %.',
          min: 0,
          max: 100,
      ),
  ]
  private ?string $disabledPercent = NULL;

  #[
      ORM\Column(type: Types::DATE_IMMUTABLE),
      Groups(['member:read', 'member:write'])
  ]
  private ?\DateTimeImmutable $joinDate = NULL;

  #[
      ORM\Column(type: Types::BOOLEAN),
      Groups(['member:read', 'member:write'])
  ]
  private ?bool $isActive = NULL;

  public function getId(): ?string {
    return $this->id;
  }

  public function getFirstname(): ?string {
    return $this->firstname;
  }

  public function setFirstname(string $firstname): static {
    $this->firstname = $firstname;

    return $this;
  }

  public function getLastname(): ?string {
    return $this->lastname;
  }

  public function setLastname(?string $lastname): static {
    $this->lastname = $lastname;

    return $this;
  }

  public function isIsStudent(): ?bool {
    return $this->isStudent;
  }

  public function setIsStudent(bool $isStudent): static {
    $this->isStudent = $isStudent;

    return $this;
  }

  public function getDob(): ?\DateTimeImmutable {
    return $this->dob;
  }

  public function setDob(\DateTimeImmutable $dob): static {
    $this->dob = $dob;

    return $this;
  }

  public function getOIB(): ?string {
    return $this->OIB;
  }

  public function setOIB(string $OIB): static {
    $this->OIB = $OIB;

    return $this;
  }

  public function getAddress(): ?string {
    return $this->address;
  }

  public function setAddress(string $address): static {
    $this->address = $address;

    return $this;
  }

  public function getCity(): ?string {
    return $this->city;
  }

  public function setCity(string $city): static {
    $this->city = $city;

    return $this;
  }

  public function getPhone(): ?string {
    return $this->phone;
  }

  public function setPhone(?string $phone): static {
    $this->phone = $phone;

    return $this;
  }

  public function getEmail(): ?string {
    return $this->email;
  }

  public function setEmail(?string $email): static {
    $this->email = $email;

    return $this;
  }

  public function getDisabledPercent(): ?string {
    return $this->disabledPercent;
  }

  public function setDisabledPercent(string $disabledPercent): static {
    $this->disabledPercent = $disabledPercent;

    return $this;
  }

  public function getJoinDate(): ?\DateTimeImmutable {
    return $this->joinDate;
  }

  public function setJoinDate(\DateTimeImmutable $joinDate): static {
    $this->joinDate = $joinDate;

    return $this;
  }

  public function isIsActive(): ?bool {
    return $this->isActive;
  }

  public function setIsActive(bool $isActive): static {
    $this->isActive = $isActive;

    return $this;
  }
}
