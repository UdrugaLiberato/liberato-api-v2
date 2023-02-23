<?php

declare(strict_types=1);

namespace App\DTO\Category;

use Doctrine\Common\Collections\ArrayCollection;

class CategoryOutput
{
    public function __construct(
        public string $id,
        public string $name,
        public ArrayCollection $questions,
        public ?string $description,
        public string $createdAt,
        public ?string $deletedAt,
        public ArrayCollection $icon,
        public int $numberOfLocationsInCategory
    ) {
    }
}
