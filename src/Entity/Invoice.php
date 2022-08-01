<?php

declare(strict_types=1);

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiResource;
use App\Controller\UpdateInvoiceController;
use App\DTO\Invoice\InvoiceInput;
use App\DTO\Invoice\InvoiceOutput;
use App\Repository\InvoiceRepository;
use DateTimeImmutable;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: InvoiceRepository::class)]
#[ApiResource(
    collectionOperations: [
        'get',
        'post' => [
            'input' => InvoiceInput::class,
            'security' => "is_granted('ROLE_ADMIN')",
            'security_message' => 'Only admin users are allowed to add invoices.',
            'input_formats' => [
                'multipart' => ['multipart/form-data'],
            ],
        ],
    ],
    itemOperations: ['get', 'delete' => [
        'security' => "is_granted('ROLE_ADMIN')",
        'security_message' => 'Only admin users are allowed to delete invoices.',
    ], 'put' => [
        'security' => "is_granted('ROLE_ADMIN')",
        'security_message' => 'Only admin users are allowed to edit invoices.',
        'deserialize' => false,
        'controller' => UpdateInvoiceController::class,
        'input_formats' => [
            'multipart' => ['multipart/form-data'],
        ],
    ]],
    output: InvoiceOutput::class
)]
class Invoice
{
    #[
        ORM\Id,
        ORM\Column(type: 'string', unique: true),
        ORM\GeneratedValue(strategy: 'CUSTOM'),
        ORM\CustomIdGenerator(class: 'doctrine.uuid_generator')
    ]
    private string $id;

    #[ORM\Column(type: 'text', nullable: true)]
    private string $description;

    #[ORM\Column(type: 'float')]
    private float $amount;

    #[ORM\Column(type: 'string', nullable: true)]
    private ?string $invoiceNumber;

    #[ORM\Column(type: 'boolean', nullable: false)]
    private bool $sendToAccountant;

    #[ORM\Column(type: 'string', nullable: false)]
    private string $currency;

    #[ORM\Column(type: 'datetime_immutable')]
    private DateTimeImmutable $payedAt;

    #[ORM\Column(type: 'datetime_immutable')]
    private DateTimeImmutable $createdAt;

    #[ORM\Column(type: 'datetime_immutable', nullable: true)]
    private DateTimeImmutable $updatedAt;

    #[ORM\Column(type: 'datetime_immutable', nullable: true)]
    private DateTimeImmutable $deletedAt;

    #[ORM\Column(type: 'array')]
    private ArrayCollection $files;

    #[ORM\ManyToOne(targetEntity: Project::class, cascade: ['persist'], inversedBy: 'invoices')]
    private Project $project;

    public function __construct()
    {
        $this->createdAt = new DateTimeImmutable('now');
        $this->files = new ArrayCollection();
    }

    public function getId(): string
    {
        return $this->id;
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

    public function getAmount(): ?float
    {
        return $this->amount;
    }

    public function setAmount(float $amount): self
    {
        $this->amount = $amount;

        return $this;
    }

    public function getPayedAt(): ?DateTimeImmutable
    {
        return $this->payedAt;
    }

    public function setPayedAt(DateTimeImmutable $payedAt): self
    {
        $this->payedAt = $payedAt;

        return $this;
    }

    public function getCurrency(): string
    {
        return $this->currency;
    }

    public function setCurrency(string $currency): void
    {
        $this->currency = $currency;
    }

    public function getInvoiceNumber(): ?string
    {
        return $this->invoiceNumber;
    }

    public function setInvoiceNumber(?string $invoiceNumber): void
    {
        $this->invoiceNumber = $invoiceNumber;
    }

    public function isSendToAccountant(): bool
    {
        return $this->sendToAccountant;
    }

    public function setSendToAccountant(bool $sendToAccountant): void
    {
        $this->sendToAccountant = $sendToAccountant;
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

    public function getFiles(): ?ArrayCollection
    {
        return $this->files;
    }

    public function setFiles(ArrayCollection $files): self
    {
        $this->files = $files;

        return $this;
    }

    public function getProject(): ?Project
    {
        return $this->project;
    }

    public function setProject(?Project $project): self
    {
        $this->project = $project;

        return $this;
    }
}
