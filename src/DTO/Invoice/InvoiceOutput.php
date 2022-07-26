<?php

declare(strict_types=1);

namespace App\DTO\Invoice;

use App\Entity\Project;
use Doctrine\Common\Collections\ArrayCollection;

class InvoiceOutput
{
    public function __construct(
        public string $description,
        public float $amount,
        public string $payedAt,
        public ArrayCollection $files,
        public Project $project
    ) {
    }
}
