<?php

declare(strict_types=1);

namespace App\DTO\BankAccount;

class BankAccountInput
{
    public string $iban;
    public string $bankName;
    public string $bankAccountHolderName;
}
