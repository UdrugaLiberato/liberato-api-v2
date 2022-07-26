<?php

declare(strict_types=1);

namespace App\DTO\DonationGiver;

class DonationGiverInput
{
    public string $name;
    public bool $approved;
    public float $moneyRequested;
    public float $moneyGiven;
    public string $dateOfApplication;
    public string $dateOfApproval;
}
