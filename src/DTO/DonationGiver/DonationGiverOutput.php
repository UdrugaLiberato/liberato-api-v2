<?php

namespace App\DTO\DonationGiver;

class DonationGiverOutput
{
    public function __construct(
        public string  $name,
        public bool    $approved,
        public float   $moneyRequested,
        public float   $moneyGiven,
        public string  $dateOfApplication,
        public ?string  $dateOfApproval,
        public string  $createdAt,
        public ?string $updatedAt,
        public ?string $deletedAt
    )
    {
    }
}