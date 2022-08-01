<?php

declare(strict_types=1);

namespace App\DTO\Invoice;

use App\Entity\Project;
use Doctrine\Common\Collections\ArrayCollection;

class InvoiceOutput
{
    public function __construct(
        public string $id,
        public string $description,
        public float $amount,
        public string $currency,
        public string $invoiceNumber,
        public bool $isSentToAccountant,
        public string $payedAt,
        public ArrayCollection $files,
        public Project $project,
        public string $createdAt,
        public string $updatedAt
    ) {
    }
}
