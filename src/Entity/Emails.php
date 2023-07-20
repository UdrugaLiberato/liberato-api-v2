<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\GetCollection;
use App\Repository\EmailsRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[
    ORM\Entity(repositoryClass: EmailsRepository::class),
    ApiResource(),
    GetCollection(
        paginationEnabled: true,
        paginationItemsPerPage: 30,
        security: "is_granted('ROLE_ADMIN')",
    ), ]
class Emails {
  #[
      ORM\Id,
      ORM\Column(type: 'string', unique: true),
      ORM\GeneratedValue(strategy: 'CUSTOM'),
      ORM\CustomIdGenerator(class: 'doctrine.uuid_generator'),
  ]
  private ?string $id = NULL;

  #[ORM\Column(length: 255)]
  private ?string $messageId = NULL;

  #[ORM\Column(length: 255)]
  private ?string $subject = NULL;

  #[ORM\Column(length: 255)]
  private ?string $fromAddress = NULL;

  #[ORM\Column(type: 'text', length: 5000, nullable: true)]
  private ?string $body = NULL;

  #[ORM\Column(length: 255)]
  private ?string $date = NULL;

  #[ORM\Column(type: Types::ARRAY)]
  private array $attachments = [];

  #[ORM\Column(length: 255)]
  private ?string $fromName = NULL;

  public function __construct() {
    $this->attachments = [];
  }

  public function getId(): string {
    return $this->id;
  }

  public function getMessageId(): ?string {
    return $this->messageId;
  }

  public function setMessageId(string $messageId): static {
    $this->messageId = $messageId;

    return $this;
  }

  public function getSubject(): ?string {
    return $this->subject;
  }

  public function setSubject(string $subject): static {
    $this->subject = $subject;

    return $this;
  }

  public function getFromAddress(): ?string {
    return $this->fromAddress;
  }

  public function setFromAddress(string $fromAddress): static {
    $this->fromAddress = $fromAddress;

    return $this;
  }

  public function getBody(): ?string {
    return $this->body;
  }

  public function setBody(string $body): static {
    $this->body = $body;

    return $this;
  }

  public function getDate(): ?string {
    return $this->date;
  }

  public function setDate(string $date): static {
    $this->date = $date;

    return $this;
  }

  public function getAttachments(): array {
    return $this->attachments;
  }

  public function setAttachments(array $attachments): static {
    $this->attachments = $attachments;

    return $this;
  }

  public function getFromName(): ?string {
    return $this->fromName;
  }

  public function setFromName(string $fromName): static {
    $this->fromName = $fromName;

    return $this;
  }
}
