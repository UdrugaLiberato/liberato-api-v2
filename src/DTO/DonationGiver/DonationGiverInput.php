<?php

namespace App\DTO\DonationGiver;

class DonationGiverInput
{
    public string $name;
    public bool $approved;
    public float $moneyRequested;
    public $moneyGiven;
    public $dateOfApplication;
    public $dateOfApproval;
}