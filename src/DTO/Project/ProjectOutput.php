<?php

declare(strict_types=1);

namespace App\DTO\Project;

use Doctrine\Common\Collections\ArrayCollection;

class ProjectOutput
{
    public function __construct(
        public string          $name,
        public string          $description,
        public string          $start,
        public string          $end,
        public float           $moneyNeeded,
        public float           $moneyGathered,
        public string          $createdAt,
        public ?string         $updatedAt,
        public ?string         $deletedAt,
        public ArrayCollection $donationGivers,
        public ArrayCollection $files
    )
    {
    }
}
