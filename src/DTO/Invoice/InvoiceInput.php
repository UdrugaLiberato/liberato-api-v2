<?php

namespace App\DTO\Invoice;

use Symfony\Component\HttpFoundation\File\UploadedFile;

class InvoiceInput
{
    public string $description;
    public float $amount;
    public string $currency;
    public string $invoiceNumber;
    public ?string $payedAt;
    public string $project;
    /** @var array<UploadedFile> $files */
    public array $files;
    public bool $sendToAccountant;

    public function __construct()
    {
        $this->sendToAccountant = $this->sendToAccountant ?? false;
    }
}