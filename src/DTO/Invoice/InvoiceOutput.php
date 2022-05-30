<?php

namespace App\DTO\Invoice;

class InvoiceOutput
{
    public function __construct(
        public string $description,
        public float  $amount,
        public string $payedAt,
        public array  $files,
        public        $project
    )
    {
    }
}