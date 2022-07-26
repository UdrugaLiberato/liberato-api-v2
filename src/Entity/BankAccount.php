<?php

declare(strict_types=1);

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiResource;
use App\DTO\BankAccount\BankAccountInput;
use App\DTO\BankAccount\BankAccountOutput;
use App\Repository\BankAccountRepository;
use DateTimeImmutable;
use Doctrine\ORM\Mapping as ORM;

#[
    ORM\Entity(repositoryClass: BankAccountRepository::class),
    ApiResource(
        collectionOperations: [
            'get' => [
                'security' => "is_granted('ROLE_ADMIN')",
                'security_message' => 'Only admin users are allowed to list users.',
            ],
            'post' => [
                'security' => "is_granted('ROLE_ADMIN')",
                'security_message' => 'Only admin users are allowed to list users.',
            ],
        ],
        input: BankAccountInput::class,
        output: BankAccountOutput::class
    )]
class BankAccount
{
    #[
        ORM\Id,
        ORM\Column(type: 'string', unique: true),
        ORM\GeneratedValue(strategy: 'CUSTOM'),
        ORM\CustomIdGenerator(class: 'doctrine.uuid_generator')
    ]
    private string $id;

    #[ORM\Column(type: 'string')]
    private string $iban;

    #[ORM\Column(type: 'float')]
    private float $amount;

    #[ORM\Column(type: 'string')]
    private string $bankName;

    #[ORM\Column(type: 'string')]
    private string $bankAccountHolderName;

    #[ORM\Column(type: 'datetime_immutable')]
    private DateTimeImmutable $createdAt;

    #[ORM\Column(type: 'datetime_immutable', nullable: true)]
    private ?DateTimeImmutable $updatedAt;

    #[ORM\Column(type: 'datetime_immutable', nullable: true)]
    private ?DateTimeImmutable $deletedAt;

    public function __construct()
    {
        $this->createdAt = new DateTimeImmutable('now');
        $this->updatedAt = null;
        $this->deletedAt = null;
        $this->amount = 0;
    }

    public function getId(): ?string
    {
        return $this->id;
    }

    public function getAmount(): ?float
    {
        return $this->amount;
    }

    public function setAmount(float $amount): self
    {
        $this->amount = $amount;

        return $this;
    }

    public function getCreatedAt(): ?DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function setCreatedAt(DateTimeImmutable $createdAt): self
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    public function getIban(): string
    {
        return $this->iban;
    }

    public function setIban(string $iban): void
    {
        $this->iban = $iban;
    }

    public function getUpdatedAt(): ?DateTimeImmutable
    {
        return $this->updatedAt;
    }

    public function setUpdatedAt(?DateTimeImmutable $updatedAt): self
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }

    public function getDeletedAt(): ?DateTimeImmutable
    {
        return $this->deletedAt;
    }

    public function setDeletedAt(?DateTimeImmutable $deletedAt): self
    {
        $this->deletedAt = $deletedAt;

        return $this;
    }

    public function getBankName(): string
    {
        return $this->bankName;
    }

    public function setBankName(string $bankName): void
    {
        $this->bankName = $bankName;
    }

    public function getBankAccountHolderName(): string
    {
        return $this->bankAccountHolderName;
    }

    public function setBankAccountHolderName(string $bankAccountHolderName): void
    {
        $this->bankAccountHolderName = $bankAccountHolderName;
    }
}
