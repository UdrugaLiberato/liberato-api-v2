<?php

declare(strict_types=1);

namespace App\DTO\BankAccount;

class BankAccountOutput
{
    public function __construct(
        public string  $iban,
        public string  $bankName,
        public string  $bankAccountHolderName,
        public float   $amount,
        public string  $createdAt,
        public ?string $updatedAt,
        public ?string $deletedAt,
    )
    {
    }
}
