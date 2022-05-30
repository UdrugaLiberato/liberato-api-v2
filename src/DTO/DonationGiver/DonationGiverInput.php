<?php

namespace App\DTO\DonationGiver;

class DonationGiverInput
{
    public string $name;
    public bool $approved;
    public float $moneyRequested;
    public float $moneyGiven;
    public $dateOfApplication;
    public  $dateOfApproval;
}