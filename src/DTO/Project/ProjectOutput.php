<?php

namespace App\DTO\Project;

class ProjectOutput
{
    public function __construct(
        public string  $name,
        public string  $description,
        public string  $start,
        public string  $end,
        public float   $moneyNeeded,
        public float   $moneyGathered,
        public string  $createdAt,
        public ?string $updatedAt,
        public ?string $deletedAt,
        public         $donationGivers,
        public         $files
    )
    {
    }
}