<?php

declare(strict_types=1);

namespace App\DTO\Location;

use App\Entity\Category;
use App\Entity\City;
use Doctrine\Common\Collections\ArrayCollection;

class LocationOutput
{
    public function __construct(
        public ArrayCollection $answers,
        public string $name,
        public string $street,
        public ?string $phone,
        public ?string $email,
        public bool $published,
        public bool $featured,
        public Category $category,
        public City $city,
        public string $createdAt,
        public string $user,
        public ?string $about,
        public ?string $updatedAt,
        public ?string $deletedAt,
        public ArrayCollection $images
    ) {
    }
}
